<?php

namespace Paysera\WalletApi;

use Paysera\WalletApi\Entity\Allowance;
use Paysera\WalletApi\Entity\Client;
use Paysera\WalletApi\Entity\ClientPermissions;
use Paysera\WalletApi\Entity\Commission;
use Paysera\WalletApi\Entity\FundsSource;
use Paysera\WalletApi\Entity\Inquiry\InquiryItem;
use Paysera\WalletApi\Entity\Item;
use Paysera\WalletApi\Entity\Limit;
use Paysera\WalletApi\Entity\Location;
use Paysera\WalletApi\Entity\Location\DayWorkingHours;
use Paysera\WalletApi\Entity\MacAccessToken;
use Paysera\WalletApi\Entity\MacCredentials;
use Paysera\WalletApi\Entity\Money;
use Paysera\WalletApi\Entity\MposCredential;
use Paysera\WalletApi\Entity\PayCategory;
use Paysera\WalletApi\Entity\Payment;
use Paysera\WalletApi\Entity\PaymentPassword;
use Paysera\WalletApi\Entity\PriceRules;
use Paysera\WalletApi\Entity\Project;
use Paysera\WalletApi\Entity\Restriction\UserRestriction;
use Paysera\WalletApi\Entity\Restrictions;
use Paysera\WalletApi\Entity\Search\Filter;
use Paysera\WalletApi\Entity\Search\Result;
use Paysera\WalletApi\Entity\Statement;
use Paysera\WalletApi\Entity\Statement\SearchFilter;
use Paysera\WalletApi\Entity\SufficientAmountRequest;
use Paysera\WalletApi\Entity\SufficientAmountResponse;
use Paysera\WalletApi\Entity\Time;
use Paysera\WalletApi\Entity\Transaction;
use Paysera\WalletApi\Entity\TransferConfiguration;
use Paysera\WalletApi\Entity\TransferInput;
use Paysera\WalletApi\Entity\TransferOutput;
use Paysera\WalletApi\Entity\TransferOutputResult;
use Paysera\WalletApi\Entity\User;
use Paysera\WalletApi\Entity\UserInformation;
use Paysera\WalletApi\Entity\Wallet;
use Paysera\WalletApi\Entity\WalletIdentifier;
use Paysera\WalletApi\Exception\LogicException;
use Paysera\WalletApi\Mapper\IdentityMapper;
use Paysera\WalletApi\Mapper\InquiryResultMapper;
use Paysera\WalletApi\Mapper\PlainValueMapper;
use Paysera\WalletApi\Mapper\TransferConfigurationMapper;

/**
 * Class for encoding and decoding entities to and from arrays
 */
class Mapper
{
    /**
     * Decodes access token object from array
     *
     *
     * @throws \InvalidArgumentException
     */
    public function decodeAccessToken(array $data): MacAccessToken
    {
        if ($data['token_type'] !== 'mac' || $data['mac_algorithm'] !== 'hmac-sha-256') {
            throw new \InvalidArgumentException('Only mac tokens with hmac-sha-256 algorithm are supported');
        }

        return MacAccessToken::create()
            ->setExpiresAt(time() + $data['expires_in'])
            ->setMacId($data['access_token'])
            ->setMacKey($data['mac_key'])
            ->setRefreshToken($data['refresh_token'] ?? null);
    }

    /**
     * Encodes payment object to array. Used for creating payment
     *
     *
     * @return array
     *
     * @throws LogicException    if some fields are invalid, ie. payment already has an ID
     */
    public function encodePayment(Payment $payment)
    {
        if ($payment->getId() !== null) {
            throw new LogicException('Cannot create already existing payment');
        }
        $result = [];
        if (($description = $payment->getDescription()) !== null) {
            $result['description'] = $description;
        }
        $price = $payment->getPrice();
        if ($price !== null) {
            $result['price_decimal'] = $price->getAmount();
            $result['currency'] = $price->getCurrency();
        }
        $commission = $payment->getCommission();
        if ($commission !== null) {
            $result['commission'] = $this->encodeCommission($commission);
        }
        $cashback = $payment->getCashback();
        if ($cashback !== null) {
            if ($price !== null && $price->getCurrency() !== $cashback->getCurrency()) {
                throw new LogicException('Price and cashback currency must be the same');
            }
            $result['cashback_decimal'] = $cashback->getAmount();
            $result['currency'] = $cashback->getCurrency();
        }
        if ($payment->hasItems()) {
            $items = [];
            foreach ($payment->getItems() as $item) {
                $items[] = $this->encodeItem($item);
            }
            $result['items'] = $items;
        }
        if (($beneficiary = $payment->getBeneficiary()) !== null) {
            $result['beneficiary'] = $this->encodeWalletIdentifier($beneficiary);
        }
        if (($freezeFor = $payment->getFreezeFor()) !== null) {
            $result['freeze_for'] = $freezeFor;
        }
        if (($parameters = $payment->getParameters()) !== null) {
            $result['parameters'] = $parameters;
        }
        if (($password = $payment->getPaymentPassword()) !== null) {
            $result['password'] = $this->encodePaymentPassword($password);
        }
        if (($priceRules = $payment->getPriceRules()) !== null) {
            $result['price_rules'] = $this->encodePriceRules($priceRules);
        }
        if (($purpose = $payment->getPurpose()) !== null) {
            $result['purpose'] = $purpose;
        }
        if (($fundsSource = $payment->getFundsSource()) !== null) {
            $result['funds_source'] = $this->encodeFundsSource($fundsSource);
        }

        if (!(isset($result['description']) && isset($result['price_decimal']) || isset($result['items']))) {
            throw new LogicException(
                'Description and price are required if items are not set',
            );
        }

        return $result;
    }

    /**
     * Encodes transaction funds sources to array
     *
     * @param \Paysera\WalletApi\Entity\FundsSource[] $fundsSources
     *
     * @throws LogicException
     *
     * @return array
     */
    public function encodeFundsSources(array $fundsSources)
    {
        $result = [];

        foreach ($fundsSources as $key => $fundsSource) {
            $result[$key] = $this->encodeFundsSource($fundsSource);
        }

        return ['funds_sources' => $result];
    }

    /**
     * Encodes payment funds source to array
     *
     *
     * @throws LogicException
     *
     * @return array
     */
    public function encodeFundsSource(FundsSource $fundsSource)
    {
        if (0 === strlen($fundsSource->getType()) && 0 === strlen($fundsSource->getDetails())) {
            throw new LogicException("Funds source type or details required");
        }

        $result = [];

        if ($fundsSource->getType() !== null) {
            $result['type'] = $fundsSource->getType();
        }

        if (strlen($fundsSource->getDetails()) > 0) {
            $result['details'] = $fundsSource->getDetails();
        }

        return $result;
    }

    /**
     * Decodes funds source object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\FundsSource
     */
    public function decodeFundsSource($data)
    {
        $fundsSource = new FundsSource();

        if (!empty($data['type'])) {
            $fundsSource->setType($data['type']);
        }
        if (!empty($data['details'])) {
            $fundsSource->setDetails($data['details']);
        }

        return $fundsSource;
    }

    /**
     * Encodes transaction restriction object to array
     *
     * @return array
     */
    public function encodeRestrictions(Restrictions $restrictions)
    {
        $result = [];
        if ($accountOwnerRestriction = $restrictions->getAccountOwnerRestriction()) {
            $requirements = [];
            if ($accountOwnerRestriction->isIdentityRequired()) {
                $requirements[] = 'identity';
            }

            $result['account_owner'] = ['type' => $accountOwnerRestriction->getType(), 'requirements' => $requirements, 'level' => $accountOwnerRestriction->getLevel()];
        }

        return $result;
    }

    /**
     * Encodes project object to array
     *
     *
     * @return array
     *
     * @throws LogicException
     */
    public function encodeProject(Project $project)
    {
        $result = [];

        if ($project->getTitle() === null) {
            throw new LogicException("Project must have title property set");
        }
        $result['title'] = $project->getTitle();

        if ($project->getDescription()) {
            $result['description'] = $project->getDescription();
        }

        if ($project->getWalletId()) {
            $result['wallet_id'] = $project->getWalletId();
        }

        return $result;
    }

    /**
     * Decodes project object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Project
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function decodeProject($data)
    {
        $project = new Project();

        $this->setProperty($project, 'id', $data['id']);
        $this->setProperty($project, 'title', $data['title']);
        if (!empty($data['description'])) {
            $this->setProperty($project, 'description', $data['description']);
        }
        if (!empty($data['wallet_id'])) {
            $this->setProperty($project, 'walletId', $data['wallet_id']);
        }

        return $project;
    }

    /**
     * Decodes payment object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Payment
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function decodePayment($data)
    {
        $payment = new Payment();
        $this->setProperty($payment, 'id', $data['id']);
        $this->setProperty($payment, 'transactionKey', $data['transaction_key']);
        $this->setProperty($payment, 'createdAt', $this->createDateTimeFromTimestamp($data['created_at']));
        $this->setProperty($payment, 'status', $data['status']);

        $payment->setPrice(
            Money::create()->setAmount($data['price_decimal'])->setCurrency($data['currency']),
        );
        if (isset($data['commission'])) {
            $payment->setCommission($this->decodeCommission($data['commission'], $data['currency']));
        }
        if (isset($data['cashback_decimal'])) {
            $payment->setCashback(
                Money::create()->setAmount($data['cashback_decimal'])->setCurrency($data['currency']),
            );
        }
        if (isset($data['wallet'])) {
            $this->setProperty($payment, 'walletId', $data['wallet']);
        }
        if (isset($data['confirmed_at'])) {
            $this->setProperty($payment, 'confirmedAt', $this->createDateTimeFromTimestamp($data['confirmed_at']));
        }
        if (isset($data['freeze_until'])) {
            $payment->setFreezeUntil($this->createDateTimeFromTimestamp($data['freeze_until']));
        }
        if (isset($data['freeze_for'])) {
            $payment->setFreezeFor($data['freeze_for']);
        }
        if (isset($data['description'])) {
            $payment->setDescription($data['description']);
        }
        if (isset($data['items'])) {
            $items = [];
            foreach ($data['items'] as $item) {
                $items[] = $this->decodeItem($item);
            }
            $payment->setItems($items);
        }
        if (isset($data['beneficiary'])) {
            $payment->setBeneficiary($this->decodeWalletIdentifier($data['beneficiary']));
        }
        if (isset($data['parameters'])) {
            $payment->setParameters($data['parameters']);
        }
        if (isset($data['password'])) {
            $payment->setPaymentPassword($this->decodePaymentPassword($data['password']));
        }
        if (isset($data['price_rules'])) {
            $payment->setPriceRules($this->decodePriceRules($data['price_rules'], $data['currency']));
        }
        if (isset($data['purpose'])) {
            $payment->setPurpose($data['purpose']);
        }
        if (isset($data['funds_source'])) {
            $payment->setFundsSource($this->decodeFundsSource($data['funds_source']));
        }

        return $payment;
    }

    /**
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Search\Result
     */
    public function decodePaymentSearchResult($data)
    {
        $payments = [];
        foreach ($data['payments'] as $payment) {
            $payments[] = $this->decodePayment($payment);
        }

        $result = new Result($payments);
        $metadata = $data['_metadata'];
        $this->setProperty($result, 'total', $metadata['total']);
        $this->setProperty($result, 'offset', $metadata['offset']);
        $this->setProperty($result, 'limit', $metadata['limit']);

        return $result;
    }

    /**
     * Encodes payment commission to array
     *
     *
     * @throws LogicException
     *
     * @return array
     */
    protected function encodeCommission(Commission $commission)
    {
        if (null === $commission->getOutCommission() && null === $commission->getInCommission()) {
            throw new LogicException("In our Out commission required");
        }

        $result = [];

        if ($commission->getOutCommission() !== null) {
            $result['out_commission_decimal'] = $commission->getOutCommission()->getAmount();
        }

        if ($commission->getInCommission() !== null) {
            $result['in_commission_decimal'] = $commission->getInCommission()->getAmount();
        }

        return $result;
    }

    /**
     * Decodes payment commssion object from array
     *
     *
     * @return \Paysera\WalletApi\Entity\Commission
     */
    protected function decodeCommission($data, $currency)
    {
        $commission = new Commission();

        if (isset($data['in_commission_decimal'])) {
            $commission->setInCommission(
                Money::create()->setAmount($data['in_commission_decimal'])->setCurrency($currency),
            );
        }

        if (isset($data['out_commission_decimal'])) {
            $commission->setOutCommission(
                Money::create()->setAmount($data['out_commission_decimal'])->setCurrency($currency),
            );
        }

        return $commission;
    }

    /**
     * Encodes item object to array
     *
     *
     * @return array
     *
     * @throws LogicException
     */
    protected function encodeItem(Item $item)
    {
        $result = [];
        if ($item->getTitle() === null || $item->getPrice() === null) {
            throw new LogicException('Each item must have title and price');
        }
        $result['title'] = $item->getTitle();
        if (($description = $item->getDescription()) !== null) {
            $result['description'] = $description;
        }
        if (($imageUri = $item->getImageUri()) !== null) {
            $result['image_uri'] = $imageUri;
        }
        $result['price_decimal'] = $item->getPrice()->getAmount();
        $result['currency'] = $item->getPrice()->getCurrency();
        $result['quantity'] = $item->getQuantity();
        if (($parameters = $item->getParameters()) !== null) {
            $result['parameters'] = $parameters;
        }
        if ($item->getTotalPrice() !== null) {
            $result['total_price_decimal'] = $item->getTotalPrice()->getAmount();
        }

        return $result;
    }

    /**
     * Decodes item object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Item
     */
    protected function decodeItem($data)
    {
        $item = new Item();
        $item->setTitle($data['title']);
        $price = Money::create()
            ->setAmount($data['price_decimal'])
            ->setCurrency($data['currency']);
        $item->setPrice($price);
        $item->setQuantity($data['quantity']);
        if (isset($data['description'])) {
            $item->setDescription($data['description']);
        }
        if (isset($data['image_uri'])) {
            $item->setImageUri($data['image_uri']);
        }
        if (isset($data['parameters'])) {
            $item->setParameters($data['parameters']);
        }
        if (isset($data['total_price_decimal'])) {
            $item->setTotalPrice(
                Money::create()
                    ->setAmount($data['total_price_decimal'])
                    ->setCurrency($data['currency']),
            );
        }

        return $item;
    }

    /**
     * Encodes allowance object to array. Used for creating allowance
     *
     *
     * @return array
     *
     * @throws LogicException    if some fields are invalid, ie. allowance already has an ID
     */
    public function encodeAllowance(Allowance $allowance)
    {
        if ($allowance->getId() !== null) {
            throw new LogicException('Cannot create already existing allowance');
        }
        $result = [];
        if (($description = $allowance->getDescription()) !== null) {
            $result['description'] = $description;
        }
        $maxPrice = $allowance->getMaxPrice();
        if ($maxPrice === null && !$allowance->hasLimits()) {
            throw new LogicException('Allowance must have max price or limits set');
        }

        $currency = null;
        if ($maxPrice !== null) {
            $currency = $maxPrice->getCurrency();
        }
        foreach ($allowance->getLimits() as $limit) {
            $currentCurrency = $limit->getMaxPrice()->getCurrency();
            if ($currency === null) {
                $currency = $currentCurrency;
            } elseif ($currency !== $currentCurrency) {
                throw new LogicException('All sums in allowance must have the same currency');
            }
        }
        $result['currency'] = $currency;

        if ($maxPrice !== null) {
            $result['max_price'] = $maxPrice->getAmountInCents();
        }

        if ($allowance->getValidFor() !== null && $allowance->getValidUntil() !== null) {
            throw new LogicException('Only one of validFor and validUntil can be provided');
        } elseif ($allowance->getValidFor() !== null) {
            $result['valid_for'] = $allowance->getValidFor();
        } elseif ($allowance->getValidUntil() !== null) {
            $result['valid_until'] = $allowance->getValidUntil()->getTimestamp();
        }

        if ($allowance->hasLimits()) {
            $result['limits'] = [];
            foreach ($allowance->getLimits() as $limit) {
                if ($limit->getMaxPrice() === null || $limit->getTime() === null) {
                    throw new LogicException('At least one limit has no price or no time');
                }
                $limitData = ['max_price' => $limit->getMaxPrice()->getAmountInCents(), 'time' => $limit->getTime()];
                $result['limits'][] = $limitData;
            }
        }

        return $result;
    }

    /**
     * Decodes allowance object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Allowance
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function decodeAllowance($data)
    {
        $allowance = new Allowance();
        $this->setProperty($allowance, 'id', $data['id']);
        $this->setProperty($allowance, 'transactionKey', $data['transaction_key']);
        $this->setProperty($allowance, 'createdAt', $this->createDateTimeFromTimestamp($data['created_at']));
        $this->setProperty($allowance, 'status', $data['status']);
        if (isset($data['wallet'])) {
            $this->setProperty($allowance, 'wallet', $data['wallet']);
        }
        if (isset($data['confirmed_at'])) {
            $this->setProperty($allowance, 'confirmedAt', $this->createDateTimeFromTimestamp($data['confirmed_at']));
        }
        if (isset($data['valid_until'])) {
            $allowance->setValidUntil($this->createDateTimeFromTimestamp($data['valid_until']));
        }
        if (isset($data['valid_for'])) {
            $allowance->setValidFor($data['valid_for']);
        }
        if (isset($data['description'])) {
            $allowance->setDescription($data['description']);
        }
        if (isset($data['max_price'])) {
            $allowance->setMaxPrice(
                Money::create()->setAmountInCents($data['max_price'])->setCurrency($data['currency']),
            );
        }
        if (isset($data['limits'])) {
            foreach ($data['limits'] as $limitInfo) {
                $price = Money::create()
                    ->setCurrency($data['currency'])
                    ->setAmountInCents($limitInfo['max_price']);
                $limit = Limit::create()
                    ->setTime($limitInfo['time'])
                    ->setMaxPrice($price)
                ;
                $allowance->addLimit($limit);
            }
        }

        return $allowance;
    }

    /**
     * Decodes transaction restriction object from array
     *
     *
     */
    public function decodeRestrictions($data): ?Restrictions
    {
        if (isset($data['account_owner'])) {
            return
                Restrictions::create()
                    ->setAccountOwnerRestriction(
                        UserRestriction::create()
                            ->setType($data['account_owner']['type'] ?? null)
                            ->setIdentityRequired(
                                isset($data['account_owner']['requirements'])
                                && in_array('identity', $data['account_owner']['requirements']),
                            )
                            ->setLevel($data['account_owner']['level'] ?? null),
                    )
            ;
        }

        return null;
    }

    /**
     * Encodes transaction object to array. Used for creating transaction
     *
     *
     * @return array
     *
     * @throws LogicException    if some fields are invalid, ie. transaction already has a key
     */
    public function encodeTransaction(Transaction $transaction)
    {
        if ($transaction->getKey() !== null) {
            throw new LogicException('Cannot create already existing transaction');
        }
        if (
            (is_countable($transaction->getPayments()) ? count($transaction->getPayments()) : 0) === 0
            && (is_countable($transaction->getPaymentIdList()) ? count($transaction->getPaymentIdList()) : 0) === 0
            && $transaction->getAllowance() === null
            && $transaction->getAllowanceId() === null
        ) {
            throw new LogicException('Transaction must have at least one payment or allowance');
        }

        $result = [];

        $payments = [];
        foreach ($transaction->getPayments() as $payment) {
            if ($payment->getId() === null) {
                $payments[] = $this->encodePayment($payment);
            } else {
                $payments[] = $payment->getId();
            }
        }
        foreach ($transaction->getPaymentIdList() as $paymentId) {
            $payments[] = $paymentId;
        }

        if ((is_countable($payments) ? count($payments) : 0) > 0) {
            $result['payments'] = $payments;
        }

        if ($transaction->getReserveFor() !== null && $transaction->getReserveUntil() !== null) {
            throw new LogicException('Only one of reserveFor and reserveUntil can be provided');
        } elseif ($transaction->getReserveFor() !== null) {
            $result['reserve_for'] = $transaction->getReserveFor();
        } elseif ($transaction->getReserveUntil() !== null) {
            $result['reserve_until'] = $transaction->getReserveUntil()->getTimestamp();
        }

        $allowance = null;
        $allowanceId = null;
        if ($transaction->getAllowance() !== null) {
            if ($transaction->getAllowance()->getId() === null) {
                $allowance = $this->encodeAllowance($transaction->getAllowance());
            } else {
                $allowanceId = $transaction->getAllowance()->getId();
            }
        } elseif ($transaction->getAllowanceId() !== null) {
            $allowanceId = $transaction->getAllowanceId();
        }
        if ($allowance !== null) {
            $result['allowance'] = ['data' => $allowance, 'optional' => $transaction->getAllowanceOptional()];
        } elseif ($allowanceId !== null) {
            $result['allowance'] = ['id' => $allowanceId, 'optional' => $transaction->getAllowanceOptional()];
        }

        $result['use_allowance'] = $transaction->getUseAllowance();
        $result['suggest_allowance'] = $transaction->getSuggestAllowance();

        if ($restrictions = $transaction->getRestrictions()) {
            $result['restrictions'] = $this->encodeRestrictions($restrictions);
        }
        if ($transaction->isAutoConfirm() !== null) {
            $result['auto_confirm'] = $transaction->isAutoConfirm();
        }
        if ($transaction->getRedirectUri() !== null) {
            $result['redirect_uri'] = $transaction->getRedirectUri();
        }
        if ($transaction->isCallbacksDisabled()) {
            $result['callback_uri'] = false;
        } elseif ($transaction->getCallbackUri() !== null) {
            $result['callback_uri'] = $transaction->getCallbackUri();
        }
        if ($transaction->getUserInformation() !== null) {
            $result['user'] = $this->encodeUserInformation($transaction->getUserInformation());
        }
        if ((is_countable($transaction->getInquiries()) ? count($transaction->getInquiries()) : 0) > 0) {
            foreach ($transaction->getInquiries() as $inquiry) {
                $result['inquiries'][] = $this->encodeInquiry($inquiry);
            }
        }

        return $result;
    }

    /**
     * Decodes transaction object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function decodeTransaction($data)
    {
        $transaction = new Transaction();
        $this->setProperty($transaction, 'key', $data['transaction_key']);
        $this->setProperty($transaction, 'createdAt', $this->createDateTimeFromTimestamp($data['created_at']));
        $this->setProperty($transaction, 'status', $data['status']);
        if (isset($data['type'])) {
            $this->setProperty($transaction, 'type', $data['type']);
        }
        if (isset($data['wallet'])) {
            $this->setProperty($transaction, 'wallet', $data['wallet']);
        }
        if (isset($data['confirmed_at'])) {
            $this->setProperty($transaction, 'confirmedAt', $this->createDateTimeFromTimestamp($data['confirmed_at']));
        }
        if (isset($data['correlation_key'])) {
            $this->setProperty($transaction, 'correlationKey', $data['correlation_key']);
        }
        if (isset($data['payments'])) {
            $payments = [];
            $paymentIdList = [];
            foreach ($data['payments'] as $paymentInfo) {
                $payment = $this->decodePayment($paymentInfo);
                $payments[] = $payment;
                $paymentIdList[] = $payment->getId();
            }
            $this->setProperty($transaction, 'payments', $payments);
            $this->setProperty($transaction, 'paymentIdList', $paymentIdList);
        }
        if (isset($data['allowance'])) {
            $allowance = $this->decodeAllowance($data['allowance']['data']);
            $this->setProperty($transaction, 'allowance', $allowance);
            $transaction->setAllowanceOptional($data['allowance']['optional']);
        }
        if (isset($data['restrictions'])) {
            $restrictions = $this->decodeRestrictions($data['restrictions']);
            $this->setProperty($transaction, 'restrictions', $restrictions);
        }
        if (isset($data['use_allowance'])) {
            $transaction->setUseAllowance($data['use_allowance']);
        }
        if (isset($data['suggest_allowance'])) {
            $transaction->setSuggestAllowance($data['suggest_allowance']);
        }
        if (isset($data['auto_confirm'])) {
            $transaction->setAutoConfirm($data['auto_confirm']);
        }
        if (isset($data['redirect_uri'])) {
            $transaction->setRedirectUri($data['redirect_uri']);
        }
        if (isset($data['callback_uri'])) {
            $transaction->setCallbackUri($data['callback_uri']);
        }
        if (isset($data['user'])) {
            $transaction->setUserInformation($this->decodeUserInformation($data['user']));
        }
        if (isset($data['location_id'])) {
            $transaction->setLocationId($data['location_id']);
        }
        if (isset($data['manager_id'])) {
            $this->setProperty($transaction, 'managerId', $data['manager_id']);
        }
        if (isset($data['inquiries'])) {
            foreach ($data['inquiries'] as $inquiryData) {
                $transaction->addInquiry($this->decodeInquiry($inquiryData));
            }
        }
        if (isset($data['reserve']) && is_array($data['reserve'])) {
            $reserve = $data['reserve'];
            if (isset($reserve['until'])) {
                $reserveUntil = (new \DateTime())->setTimestamp($reserve['until']);
                $this->setProperty($transaction, 'reserveUntil', $reserveUntil);
            }
            if (isset($reserve['for'])) {
                $this->setProperty($transaction, 'reserveFor', $reserve['for']);
            }
        }

        return $transaction;
    }

    /**
     * @param array $data
     *
     * @return InquiryResult
     */
    public function decodeInquiryResult($data)
    {
        $valueProviders = [InquiryItem::TYPE_USER_IDENTITY =>
            new IdentityMapper(), InquiryItem::TYPE_PERSON_CODE =>
            new PlainValueMapper()];

        $mapper = new InquiryResultMapper($valueProviders);

        return $mapper->mapToEntity($data);
    }

    /**
     * @param array $data
     *
     * @return InquiryResult[]
     */
    public function decodeInquiryResults($data)
    {
        $inquiryItems = [];

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $inquiryItems[] = $this->decodeInquiryResult($item);
            }
        }

        return $inquiryItems;
    }

    /**
     * @param \Paysera\WalletApi\Entity\TransactionPrice[] $transactionPrices
     *
     * @return array
     */
    public function encodeTransactionPrices(array $transactionPrices)
    {
        $result = [];
        foreach ($transactionPrices as $transactionPrice) {
            $result[$transactionPrice->getPaymentId()] = ['price' => $transactionPrice->getPrice()->getAmountInCents(), 'currency' => $transactionPrice->getPrice()->getCurrency()];
        }

        return $result;
    }

    /**
     * Encodes payer
     *
     *
     * @return array
     */
    public function encodePayer(WalletIdentifier $walletIdentifier)
    {
        return ['payer' => $this->encodeIdentifier($walletIdentifier)];
    }

    /**
     * Encodes identifier
     *
     *
     * @return array
     */
    public function encodeIdentifier(WalletIdentifier $walletIdentifier)
    {
        if ($walletIdentifier->getCard() !== null) {
            return ['card' => ['issuer' => $walletIdentifier->getCard()->getIssuer(), 'number' => $walletIdentifier->getCard()->getNumber()]];
        }

        if ($walletIdentifier->getEmail() !== null) {
            return ['email' => $walletIdentifier->getEmail()];
        }

        if ($walletIdentifier->getId() !== null) {
            return ['id' => $walletIdentifier->getId()];
        }

        if ($walletIdentifier->getPhone() !== null) {
            return ['phone' => $walletIdentifier->getPhone()];
        }

        return [];
    }

    /**
     * Encodes user information object to array
     *
     *
     * @return array
     */
    public function encodeUserInformation(UserInformation $userInformation)
    {
        return ['email' => $userInformation->getEmail()];
    }

    /**
     * Encodes user object to array
     *
     *
     * @return array
     */
    public function encodeUser(User $user)
    {
        $data = [];
        if ($user->getEmail() !== null) {
            $data['email'] = $user->getEmail();
        }
        if ($user->getPhone() !== null) {
            $data['phone'] = $user->getPhone();
        }
        if ($identity = $user->getIdentity()) {
            $data['identity'] = $this->encodeUserIdentity($identity);
        }
        if ($address = $user->getAddress()) {
            $data['address'] = $this->encodeUserAddress($address);
        }

        return $data;
    }

    /**
     * Encodes user identity object to array
     *
     *
     * @return array
     */
    public function encodeUserIdentity(User\Identity $identity)
    {
        return (new IdentityMapper())->mapFromEntity($identity);
    }

    /**
     * Encodes user address object to array
     *
     *
     * @return array
     */
    public function encodeUserAddress(User\Address $address)
    {
        $data = [];
        if ($address->getCountry() !== null) {
            $data['country'] = $address->getCountry();
        }
        if ($address->getCity() !== null) {
            $data['city'] = $address->getCity();
        }
        if ($address->getStreet() !== null) {
            $data['street'] = $address->getStreet();
        }
        if ($address->getPostIndex() !== null) {
            $data['post_index'] = $address->getPostIndex();
        }

        return $data;
    }

    /**
     * Encodes money object to array
     *
     *
     * @return array
     */
    public function encodePrice(Money $price)
    {
        return ['price' => $price->getAmountInCents(), 'currency' => $price->getCurrency()];
    }

    /**
     * Encodes money
     *
     *
     * @return array
     */
    public function encodeMoney(Money $money)
    {
        return ['amount' => $money->getAmountInCents(), 'currency' => $money->getCurrency()];
    }

    /**
     * Decodes user information object from array
     *
     *
     */
    public function decodeUserInformation(array $data): UserInformation
    {
        return UserInformation::create()
            ->setEmail($data['email']);
    }

    /**
     * Encodes wallet identifier object to array
     *
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function encodeWalletIdentifier(WalletIdentifier $walletIdentifier)
    {
        if ($walletIdentifier->getId() !== null) {
            return ['id' => $walletIdentifier->getId()];
        } elseif ($walletIdentifier->getEmail() !== null) {
            return ['email' => $walletIdentifier->getEmail()];
        } elseif ($walletIdentifier->getPhone() !== null) {
            return ['phone' => $walletIdentifier->getPhone()];
        } elseif ($walletIdentifier->getBarcode() !== null) {
            return ['barcode' => $walletIdentifier->getBarcode()];
        }

        throw new \InvalidArgumentException('Wallet identifier has no identifier set');
    }

    /**
     * Decodes wallet identifier object from array
     *
     *
     * @return \Paysera\WalletApi\Entity\WalletIdentifier
     */
    public function decodeWalletIdentifier(array $data)
    {
        $walletIdentifier = new WalletIdentifier();
        if (is_int($data)) {
            $walletIdentifier->setId($data);
        } elseif (is_array($data)) {
            if (isset($data['id'])) {
                $walletIdentifier->setId($data['id']);
            }
            if (isset($data['email'])) {
                $walletIdentifier->setEmail($data['email']);
            }
            if (isset($data['phone'])) {
                $walletIdentifier->setPhone($data['phone']);
            }
        }

        return $walletIdentifier;
    }

    /**
     * Encodes paymentPassword object to array
     *
     *
     * @return array
     *
     * @throws LogicException
     */
    public function encodePaymentPassword(PaymentPassword $paymentPassword)
    {
        $result = [];

        if ($paymentPassword->getType() === null) {
            throw new LogicException('Password type must be provided');
        }
        if (
            $paymentPassword->getType() === PaymentPassword::TYPE_PROVIDED
            && $paymentPassword->getValue() === null
        ) {
            throw new LogicException('Password value must be provided');
        }
        if ($paymentPassword->getStatus() !== null) {
            throw new LogicException('Status for payment password is read only');
        }

        $result['type'] = $paymentPassword->getType();

        if ($paymentPassword->getValue() !== null) {
            $result['value'] = $paymentPassword->getValue();
        }
        if ($paymentPassword->getOptional() !== null) {
            $result['optional'] = $paymentPassword->getOptional();
        }
        if ($paymentPassword->getCancelable() !== null) {
            $result['cancelable'] = $paymentPassword->getCancelable();
        }

        return $result;
    }

    /**
     * Decodes paymentPassword object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\PaymentPassword
     */
    public function decodePaymentPassword($data)
    {
        $paymentPassword = new PaymentPassword();

        if (isset($data['type'])) {
            $paymentPassword->setType($data['type']);
        }
        if (isset($data['status'])) {
            $paymentPassword->setStatus($data['status']);
        }

        return $paymentPassword;
    }

    /**
     * Encodes priceRules object to array
     *
     *
     * @return array
     *
     * @throws LogicException
     */
    public function encodePriceRules(PriceRules $priceRules)
    {
        $result = [];

        if ($priceRules->getMin() !== null) {
            $result['min'] = $priceRules->getMin()->getAmountInCents();
        }
        if ($priceRules->getMax() !== null) {
            $result['max'] = $priceRules->getMax()->getAmountInCents();
        }
        if ((is_countable($priceRules->getChoices()) ? count($priceRules->getChoices()) : 0) > 0) {
            $result['choices'] = [];
            foreach ($priceRules->getChoices() as $choice) {
                $result['choices'][] = $choice->getAmountInCents();
            }
        }

        return $result;
    }

    /**
     * Decodes priceRules object from array
     *
     * @param array  $data
     * @param string $currency
     *
     * @return \Paysera\WalletApi\Entity\PriceRules
     */
    public function decodePriceRules($data, $currency)
    {
        $priceRules = new PriceRules();

        if (isset($data['min'])) {
            $priceRules->setMin(
                Money::create()
                    ->setAmountInCents($data['min'])
                    ->setCurrency($currency),
            );
        }
        if (isset($data['max'])) {
            $priceRules->setMax(
                Money::create()
                    ->setAmountInCents($data['max'])
                    ->setCurrency($currency),
            );
        }
        if (isset($data['choices'])) {
            foreach ($data['choices'] as $choice) {
                $priceRules->addChoice(
                    Money::create()
                        ->setAmountInCents($choice)
                        ->setCurrency($currency),
                );
            }
        }

        return $priceRules;
    }

    /**
     * Decodes wallet object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Wallet
     */
    public function decodeWallet($data)
    {
        $wallet = new Wallet();

        $this->setProperty($wallet, 'id', $data['id']);
        $this->setProperty($wallet, 'owner', $data['owner']);
        if (isset($data['account'])) {
            $this->setProperty($wallet, 'account', $this->decodeAccount($data['account']));
        }

        return $wallet;
    }

    /**
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Wallet\Account
     */
    protected function decodeAccount($data)
    {
        $account = new Wallet\Account();

        $this->setProperty($account, 'number', $data['number']);
        if (isset($data['owner_title'])) {
            $this->setProperty($account, 'ownerTitle', $data['owner_title']);
        }
        if (isset($data['owner_display_name'])) {
            $this->setProperty($account, 'ownerDisplayName', $data['owner_display_name']);
        }
        if (isset($data['description'])) {
            $this->setProperty($account, 'description', $data['description']);
        }
        if (isset($data['type'])) {
            $this->setProperty($account, 'type', $data['type']);
        }
        if (isset($data['user_id'])) {
            $this->setProperty($account, 'userId', $data['user_id']);
        }

        if (isset($data['ibans'])) {
            $this->setProperty($account, 'ibans', $this->decodeIbans($data['ibans']));
        }

        if (isset($data['main_iban'])) {
            $this->setProperty($account, 'mainIban', $data['main_iban']);
        }

        return $account;
    }

    /**
     *
     * @return array
     */
    public function decodeIbans($data)
    {
        $result = [];
        foreach ($data as $key => $item) {
            $result[$key] = $this->decodeIban($item);
        }

        return $result;
    }

    /**
     *
     * @return string
     */
    public function decodeIban($data)
    {
        if (is_string($data)) {
            $data = trim($data);
        }

        return $data;
    }

    /**
     *
     * @return \Paysera\WalletApi\Entity\Wallet[]
     */
    public function decodeWallets($data)
    {
        $result = [];
        foreach ($data as $key => $item) {
            $result[$key] = $this->decodeWallet($item);
        }

        return $result;
    }

    /**
     * Decodes wallet balance object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Wallet\Balance
     */
    public function decodeWalletBalance($data)
    {
        $balance = new Wallet\Balance();

        foreach ($data as $currency => $balanceData) {
            $balance->setCurrencyBalanceDecimal(
                $currency,
                $balanceData['at_disposal_decimal'] ?? null,
                $balanceData['reserved_decimal'] ?? null,
            );

            $balance->setCurrencyBalance(
                $currency,
                $balanceData['at_disposal'] ?? 0,
                $balanceData['reserved'] ?? 0,
            );
        }

        return $balance;
    }

    /**
     * Encodes statement filter entity to an array
     *
     *
     * @return array
     */
    public function encodeFilter(Filter $filter)
    {
        $data = [];
        if ($filter->getLimit() !== null) {
            $data['limit'] = $filter->getLimit();
        }
        if ($filter->getOffset() !== null) {
            $data['offset'] = $filter->getOffset();
        }

        return $data;
    }

    /**
     * Encodes statement filter entity to an array
     *
     *
     * @return array
     */
    public function encodeStatementFilter(SearchFilter $filter)
    {
        $data = [];
        if ($filter->getLimit() !== null) {
            $data['limit'] = $filter->getLimit();
        }
        if ($filter->getOffset() !== null) {
            $data['offset'] = $filter->getOffset();
        }
        if ((is_countable($filter->getCurrencies()) ? count($filter->getCurrencies()) : 0) > 0) {
            $data['currency'] = implode(',', $filter->getCurrencies());
        }
        if ($filter->getFromDate() !== null) {
            $data['from'] = $filter->getFromDate()->getTimestamp();
        }
        if ($filter->getToDate() !== null) {
            $data['to'] = $filter->getToDate()->getTimestamp();
        }

        return $data;
    }

    /**
     * Decodes statement search result from data array
     *
     *
     * @return \Paysera\WalletApi\Entity\Search\Result|\Paysera\WalletApi\Entity\Statement[]
     */
    public function decodeStatementSearchResult($data)
    {
        $statements = [];
        foreach ($data['statements'] as $statementData) {
            $statements[] = $this->decodeStatement($statementData);
        }
        $result = new Result($statements);
        $metadata = $data['_metadata'];
        $this->setProperty($result, 'total', $metadata['total']);
        $this->setProperty($result, 'offset', $metadata['offset']);
        $this->setProperty($result, 'limit', $metadata['limit']);

        return $result;
    }

    /**
     * Decodes statement entity from data array
     *
     *
     * @return \Paysera\WalletApi\Entity\Statement
     */
    public function decodeStatement($data)
    {
        $statement = new Statement();
        $this->setProperty($statement, 'id', $data['id']);
        $this->setProperty($statement, 'amount', new Money($data['amount'], $data['currency']));
        $this->setProperty($statement, 'date', new \DateTime('@' . $data['date']));
        $this->setProperty($statement, 'details', $data['details']);
        $this->setProperty($statement, 'direction', $data['direction']);
        if (isset($data['type'])) {
            $this->setProperty($statement, 'type', $data['type']);
        }
        if (isset($data['other_party'])) {
            $this->setProperty($statement, 'otherParty', $this->decodeStatementParty($data['other_party']));
        }
        if (isset($data['transfer_id'])) {
            $this->setProperty($statement, 'transferId', $data['transfer_id']);
        }
        if (isset($data['reference_number'])) {
            $this->setProperty($statement, 'referenceNumber', $data['reference_number']);
        }

        return $statement;
    }

    /**
     * Decodes statement party object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Statement\Party
     */
    public function decodeStatementParty($data)
    {
        $party = new Statement\Party();
        $this->setProperties($party, $data, ['name', 'code', 'bic']);
        if (isset($data['account_number'])) {
            $this->setProperty($party, 'accountNumber', $data['account_number']);
        }
        if (!empty($data['display_name']) && empty($data['name'])) {
            $this->setProperty($party, 'name', $data['display_name']);
        }

        return $party;
    }

    /**
     * Decodes user object from array
     *
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\User
     */
    public function decodeUser($data)
    {
        $user = new User();

        $this->setProperty($user, 'id', $data['id']);
        if (isset($data['email'])) {
            $this->setProperty($user, 'email', $data['email']);
        }
        if (isset($data['phone'])) {
            $this->setProperty($user, 'phone', $data['phone']);
        }
        if (isset($data['display_name'])) {
            $this->setProperty($user, 'displayName', $data['display_name']);
        }
        if (isset($data['dob'])) {
            $this->setProperty($user, 'dob', $data['dob']);
        }
        if (isset($data['gender'])) {
            $this->setProperty($user, 'gender', $data['gender']);
        }
        if (isset($data['address'])) {
            $this->setProperty($user, 'address', $this->decodeAddress($data['address']));
        }
        if (isset($data['identity'])) {
            $this->setProperty($user, 'identity', $this->decodeIdentity($data['identity']));
        }
        if (isset($data['wallets'])) {
            $this->setProperty($user, 'wallets', $data['wallets']);
        }
        if (isset($data['type'])) {
            $this->setProperty($user, 'type', $data['type']);
        }
        if (isset($data['company_code'])) {
            $this->setProperty($user, 'companyCode', $data['company_code']);
        }
        if (isset($data['identification_level'])) {
            $this->setProperty($user, 'identificationLevel', $data['identification_level']);
        }
        if (isset($data['locale'])) {
            $this->setProperty($user, 'locale', $data['locale']);
        }
        if (isset($data['pep'])) {
            $peps = [];
            foreach ($data['pep'] as $pep) {
                $peps[] = $this->decodePep($pep);
            }
            $this->setProperty($user, 'politicallyExposedPersons', $peps);
        }

        return $user;
    }

    /**
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\User\PoliticallyExposedPerson
     */
    public function decodePep($data)
    {
        $politicallyExposedPerson = new User\PoliticallyExposedPerson();

        $this->setProperty($politicallyExposedPerson, 'name', $data['name']);
        $this->setProperty($politicallyExposedPerson, 'relation', $data['relation']);
        $this->setProperty($politicallyExposedPerson, 'positions', $data['positions']);

        return $politicallyExposedPerson;
    }

    /**
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\User\PoliticallyExposedPerson[]
     */
    public function decodePes($data)
    {
        $pes = [];

        if (empty($data)) {
            return $pes;
        }

        foreach ($data as $item) {
            $pes[] = $this->decodePep($item);
        }

        return $pes;
    }

    /**
     *
     * @return \Paysera\WalletApi\Entity\User\Address
     */
    public function decodeAddress($data)
    {
        $address = new User\Address();

        $this->setProperty($address, 'country', $data['country']);
        $this->setProperty($address, 'city', $data['city']);
        $this->setProperty($address, 'street', $data['street']);
        if (isset($data['post_index'])) {
            $this->setProperty($address, 'postIndex', $data['post_index']);
        }

        return $address;
    }

    /**
     *
     * @return \Paysera\WalletApi\Entity\User\Identity
     */
    public function decodeIdentity($data)
    {
        return (new IdentityMapper())->mapToEntity($data);
    }

    /**
     * Decode day working hours
     *
     * @param string $day
     *
     * @return Location_DayWorkingHours
     */
    public function decodeDayWorkingHours($day, array $data)
    {
        return DayWorkingHours::create()
            ->setOpeningTime($this->decodeTime($data['opening_time']))
            ->setClosingTime($this->decodeTime($data['closing_time']))
            ->setDay($day);
    }

    /**
     * Encode day working hours
     *
     * @param Location_DayWorkingHours $object
     *
     * @return array
     */
    public function encodeDayWorkingHours(DayWorkingHours $object)
    {
        return [$object->getDay() => ['opening_time' => $this->encodeTime(
            $object->getOpeningTime(),
        ), 'closing_time' => $this->encodeTime(
            $object->getClosingTime(),
        )]];
    }

    /**
     * Encode location
     *
     *
     * @return array
     */
    public function encodeLocation(Location $object)
    {
        $workingHours = [];
        foreach ($object->getWorkingHours() as $dayWorkingHours) {
            $workingHours = array_merge($workingHours, $this->encodeDayWorkingHours($dayWorkingHours));
        }

        $prices = [];
        foreach ($object->getPrices() as $price) {
            $prices[] = $this->encodeLocationPrice($price);
        }

        $services = $this->encodeLocationServices(
            $object->getServices(),
            $object->getPayCategories(),
            $object->getCashInTypes(),
            $object->getCashOutTypes(),
        );

        return ['id' => $object->getId(), 'title' => $object->getTitle(), 'description' => $object->getDescription(), 'address' => $object->getAddress(), 'lat' => $object->getLat(), 'lng' => $object->getLng(), 'radius' => $object->getRadius(), 'working_hours' => $workingHours, 'prices' => $prices, 'status' => $object->getStatus(), 'services' => $services, 'public' => $object->getPublic()];
    }

    /**
     * Decodes location
     *
     *
     * @return Location
     */
    public function decodeLocation(array $data)
    {
        $location = new Location();

        $location->setId($data['id']);
        $location->setTitle($data['title']);
        if (!empty($data['description'])) {
            $location->setDescription($data['description']);
        }
        $location->setAddress($data['address']);
        $location->setRadius($data['radius']);

        if (!empty($data['lat'])) {
            $location->setLat((float)$data['lat']);
        }

        if (!empty($data['lng'])) {
            $location->setLng((float)$data['lng']);
        }

        if (!empty($data['prices'])) {
            $result = [];
            foreach ($data['prices'] as $price) {
                $result[] = $this->decodeLocationPrice($price);
            }
            $location->setPrices($result);
        }

        if (!empty($data['working_hours'])) {
            foreach ($data['working_hours'] as $day => $dayWorkingHour) {
                $location->addWorkingHours(
                    $this->decodeDayWorkingHours($day, $dayWorkingHour),
                );
            }
        }

        if (!empty($data['images']['pin_open'])) {
            $location->setImagePinOpen($data['images']['pin_open']);
        }
        if (!empty($data['images']['pin_closed'])) {
            $location->setImagePinClosed($data['images']['pin_closed']);
        }

        if (!empty($data['services'])) {
            $services = [];
            foreach ($data['services'] as $key => $val) {
                if (!empty($val['available']) && $val['available'] === true) {
                    $services[] = $key;
                }
            }
            $location->setServices($services);
            if (!empty($data['services'][Location::SERVICE_TYPE_PAY]['categories'])) {
                $location->setPayCategories(
                    $data['services'][Location::SERVICE_TYPE_PAY]['categories'],
                );
            }
            if (!empty($data['services'][Location::SERVICE_TYPE_CASH_IN]['types'])) {
                $location->setCashInTypes(
                    $data['services'][Location::SERVICE_TYPE_CASH_IN]['types'],
                );
            }
            if (!empty($data['services'][Location::SERVICE_TYPE_CASH_OUT]['types'])) {
                $location->setCashOutTypes(
                    $data['services'][Location::SERVICE_TYPE_CASH_OUT]['types'],
                );
            }
        }

        if (!empty($data['status'])) {
            $location->setStatus($data['status']);
        }

        if (isset($data['public'])) {
            $location->setPublic((bool)$data['public']);
        }

        return $location;
    }

    /**
     * @return array
     */
    public function encodeLocationFilter(Location\SearchFilter $filter)
    {
        $data = [];
        if ($filter->getLimit() !== null) {
            $data['limit'] = $filter->getLimit();
        }
        if ($filter->getOffset() !== null) {
            $data['offset'] = $filter->getOffset();
        }

        if ($filter->getLat() !== null && $filter->getLng() !== null) {
            $data['lat'] = $filter->getLat();
            $data['lng'] = $filter->getLng();

            if ($filter->getDistance() !== null) {
                $data['distance'] = $filter->getDistance();
            }
        }
        if ((is_countable($filter->getStatuses()) ? count($filter->getStatuses()) : 0) > 0) {
            $data['status'] = implode(',', $filter->getStatuses());
        }
        if ($filter->getUpdatedAfter() !== null) {
            $data['updated_after'] = $filter->getUpdatedAfter()->getTimestamp();
        }
        if ($filter->getPayCategory() !== null) {
            $data['pay_category'] = implode(',', $filter->getPayCategory());
        }
        if ($filter->getLocationServices() !== null) {
            $data['service'] = implode(',', $filter->getLocationServices());
        }

        return $data;
    }

    /**
     * @param Client_SearchFilter $filter
     *
     * @return array
     */
    public function encodeClientFilter(Entity\Client\SearchFilter $filter)
    {
        $data = [];
        if ($filter->getLimit() !== null) {
            $data['limit'] = $filter->getLimit();
        }
        if ($filter->getOffset() !== null) {
            $data['offset'] = $filter->getOffset();
        }

        if ($filter->getProjectId() !== null) {
            $data['project_id'] = $filter->getProjectId();
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\Search\Result|Location[]
     */
    public function decodeLocationSearchResult($data)
    {
        $locations = [];
        foreach ($data['locations'] as $locationData) {
            $locations[] = $this->decodeLocation($locationData);
        }

        $result = new Result($locations);
        $metadata = $data['_metadata'];
        $this->setProperty($result, 'total', $metadata['total']);
        $this->setProperty($result, 'offset', $metadata['offset']);
        $this->setProperty($result, 'limit', $metadata['limit']);

        return $result;
    }

    /**
     * @return \Paysera\WalletApi\Entity\Search\Result|Client[]
     */
    public function decodeClientSearchResult(array $data)
    {
        $clients = [];
        foreach ($data['clients'] as $clientData) {
            $clients[] = $this->decodeClient($clientData);
        }

        $result = new Result($clients);
        $metadata = $data['_metadata'];
        $this->setProperty($result, 'total', $metadata['total']);
        $this->setProperty($result, 'offset', $metadata['offset']);
        $this->setProperty($result, 'limit', $metadata['limit']);

        return $result;
    }

    /**
     * @param array $data
     *
     * @return \Paysera\WalletApi\Entity\PayCategory[]
     */
    public function decodeLocationPayCategories($data)
    {
        $result = [];

        foreach ($data as $payCategoryData) {
            $payCategory = new PayCategory();
            $payCategory->setId($payCategoryData['id']);
            $payCategory->setTitle($payCategoryData['title']);
            if (!empty($payCategoryData['parent_id'])) {
                $payCategory->setParentId($payCategoryData['parent_id']);
            }
            $result[$payCategory->getId()] = $payCategory;
        }

        /** @var \Paysera\WalletApi\Entity\PayCategory $cat */
        foreach ($result as $cat) {
            if ($cat->getParentId() !== null) {
                $cat->setParent($result[$cat->getParentId()]);
            }
        }

        return $result;
    }


    /**
     * Encodes time
     *
     *
     * @return string
     */
    public function encodeTime(Time $object)
    {
        return $object->getHours() . ':' . $object->getMinutes();
    }

    /**
     * Decode time
     *
     * @param string $input
     *
     * @return \Paysera\WalletApi\Entity\Time
     */
    public function decodeTime($input)
    {
        $time = explode(':', $input);

        return new Time(
            (int)$time[0] !== 24 ? $time[0] : 0,
            $time[1],
        );
    }

    /**
     * Encodes location price
     *
     *
     * @return array
     *
     * @throws LogicException
     */
    public function encodeLocationPrice(Location_Price $locationPrice)
    {
        if ($locationPrice->getType() === null) {
            throw new LogicException("Location price must have type property set");
        }

        $result = ['title' => $locationPrice->getTitle(), 'type' => $locationPrice->getType()];

        if ($locationPrice->isPrice()) {
            $result['price'] = $this->encodeMoney($locationPrice->getPrice());
        }

        return $result;
    }

    /**
     * Decodes location price
     *
     *
     * @return Location_Price
     */
    public function decodeLocationPrice(array $data)
    {
        $locationPrice = new Location_Price();

        $locationPrice->setTitle($data['title']);

        if ($data['type'] == Location_Price::TYPE_PRICE) {
            $locationPrice->setPrice($this->decodeMoney($data['price']));
            $locationPrice->markAsPrice();
        } else {
            $locationPrice->markAsOffer();
        }

        return $locationPrice;
    }

    /**
     * Convert service list to associative array
     *
     *
     * @return array
     */
    public function encodeLocationServices(
        array $services,
        array $categories,
        array $cashInTypes,
        array $cashOutTypes
    ) {
        $data = [];
        foreach ($services as $serviceName) {
            $data[$serviceName] = ['available' => true];
        }

        if (isset($data[Location::SERVICE_TYPE_PAY]) && count($categories) > 0) {
            $data[Location::SERVICE_TYPE_PAY]['categories'] = $categories;
        }
        if (isset($data[Location::SERVICE_TYPE_CASH_IN]) && count($cashInTypes) > 0) {
            $data[Location::SERVICE_TYPE_CASH_IN]['types'] = $cashInTypes;
        }
        if (isset($data[Location::SERVICE_TYPE_CASH_OUT]) && count($cashOutTypes) > 0) {
            $data[Location::SERVICE_TYPE_CASH_OUT]['types'] = $cashOutTypes;
        }

        return $data;
    }

    /**
     *
     * @return array
     */
    public function encodePin($pin)
    {
        return ['pin' => $pin];
    }

    /**
     * Decodes Money object from array
     *
     * @param array $data
     *
     * @return Money
     */
    public function decodeMoney($data)
    {
        return Money::create()->setAmountInCents($data['amount'])->setCurrency($data['currency']);
    }

    /**
     * Encodes client permissions
     *
     *
     */
    public function encodeClientPermissions(ClientPermissions $permissions): array
    {
        return $permissions->getScopes();
    }

    /**
     * Decodes client permissions
     *
     *
     * @return ClientPermissions
     */
    public function decodeClientPermissions(array $data)
    {
        return ClientPermissions::create()
            ->setScopes($data);
    }

    /**
     * Decodes client
     *
     *
     * @return Client
     */
    public function decodeClient(array $data)
    {
        $client = Client::create()
            ->setId($data['id'])
            ->setType($data['type'])
            ->setPermissions($this->decodeClientPermissions($data['permissions']));

        $this->setProperty($client, 'title', $data['title']);

        if (!empty($data['project'])) {
            $client->setMainProject($this->decodeProject($data['project']));
            $client->setMainProjectId($client->getMainProject()->getId());
        }

        $hosts = [];
        foreach ($data['hosts'] as $host) {
            $hosts[] = $this->decodeHost($host);
        }
        $client->setHosts($hosts);

        if (!empty($data['credentials'])) {
            $client->setCredentials($this->decodeMacCredentials($data['credentials']));
        }

        if (!empty($data['service_agreement_id'])) {
            $client->setServiceAgreementId($data['service_agreement_id']);
        }

        return $client;
    }

    /**
     * Encodes client
     *
     *
     * @return array
     */
    public function encodeClient(Client $client)
    {
        if ($client->getMainProject() && $client->getMainProject()->getId()) {
            $projectId = $client->getMainProject()->getId();
        } else {
            $projectId = $client->getMainProjectId();
        }

        $result = ['type' => $client->getType()];

        if ((is_countable($client->getPermissions()->getScopes()) ? count(
            $client->getPermissions()->getScopes(),
        ) : 0) > 0) {
            $result['permissions'] = $this->encodeClientPermissions($client->getPermissions());
        }
        if (!empty($projectId)) {
            $result['project_id'] = $projectId;
        }

        if ((is_countable($client->getHosts()) ? count($client->getHosts()) : 0) > 0) {
            $result['hosts'] = [];
            foreach ($client->getHosts() as $host) {
                $result['hosts'][] = $this->encodeHost($host);
            }
        }

        if ($client->getServiceAgreementId() !== null) {
            $result['service_agreement_id'] = $client->getServiceAgreementId();
        }

        return $result;
    }

    /**
     * Decodes host
     *
     *
     * @return Client\Host
     */
    public function decodeHost(array $data)
    {
        $host = Client\Host::create()->setHost($data['host']);

        if (!empty($data['path'])) {
            $host->setPath($data['path']);
        }

        if (!empty($data['port'])) {
            $host->setPort($data['port']);
        }

        if (!empty($data['protocol'])) {
            $host->setProtocol($data['protocol']);
        }

        if (!empty($data['any_port'])) {
            $host->markAsAnyPort();
        }

        if (!empty($data['any_subdomain'])) {
            $host->markAsAnySubdomain();
        }

        return $host;
    }

    /**
     * Encodes host
     *
     *
     * @return array
     *
     * @throws LogicException
     */
    public function encodeHost(Client\Host $host)
    {
        if (!$host->getHost()) {
            throw new LogicException('Host must be provided');
        }

        return ['host' => $host->getHost(), 'port' => $host->getPort(), 'path' => $host->getPath(), 'protocol' => $host->getProtocol(), 'any_port' => $host->isAnyPort(), 'any_subdomain' => $host->isAnySubdomain()];
    }

    /**
     * @return MacCredentials
     */
    public function decodeMacCredentials(array $data)
    {
        return MacCredentials::create()
            ->setMacId($data['access_token'])
            ->setMacKey($data['mac_key'])
            ->setAlgorithm($data['mac_algorithm']);
    }

    protected function setProperties($object, array $data, array $properties)
    {
        foreach ($properties as $propertyName) {
            if (isset($data[$propertyName])) {
                $this->setProperty($object, $propertyName, $data[$propertyName]);
            }
        }
    }

    /**
     * Sets property to object. Property can be inaccessible (protected/private)
     *
     * @param object $object
     * @param string $property
     */
    protected function setProperty($object, $property, mixed $value)
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionProperty = $reflectionObject->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * Creates \DateTime object from integer UNIX timestamp
     *
     * @param integer $timestamp
     *
     */
    protected function createDateTimeFromTimestamp($timestamp): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);

        return $dateTime;
    }

    public function decodeInquiry(array $inquiryData)
    {
        $inquiry = new Entity\Inquiry\Inquiry();

        if (isset($inquiryData['identifier'])) {
            $inquiry->setIdentifier($inquiryData['identifier']);
        }
        if (isset($inquiryData['type'])) {
            $inquiry->setType($inquiryData['type']);
        }
        if (isset($inquiryData['description'])) {
            $inquiry->setDescription($inquiryData['description']);
        }
        if (isset($inquiryData['status'])) {
            $inquiry->setStatus($inquiryData['status']);
        }
        if (isset($inquiryData['items'])) {
            foreach ($inquiryData['items'] as $itemData) {
                $inquiry->addInquiryItem($this->decodeInquiryItem($itemData));
            }
        }

        return $inquiry;
    }

    public function decodeInquiryItem(array $itemData)
    {
        $item = new InquiryItem();

        if (isset($itemData['identifier'])) {
            $item->setIdentifier($itemData['identifier']);
        }
        if (isset($itemData['type'])) {
            $item->setType($itemData['type']);
        }
        if (isset($itemData['title'])) {
            $item->setTitle($itemData['title']);
        }

        return $item;
    }

    public function encodeInquiryItem(InquiryItem $item)
    {
        $data = ['identifier' => $item->getIdentifier(), 'type' => $item->getType(), 'title' => $item->getTitle()];

        return array_filter($data);
    }

    public function encodeInquiry(Inquiry $inquiry)
    {
        $data = ['identifier' => $inquiry->getIdentifier(), 'type' => $inquiry->getType(), 'description' => $inquiry->getDescription()];

        foreach ($inquiry->getInquiryItems() as $item) {
            $data['items'][] = $this->encodeInquiryItem($item);
        }

        return array_filter($data);
    }

    /**
     * Encodes MPOS Credential
     *
     *
     * @return array
     */
    public function encodeMposCredential(MposCredential $mposCredential)
    {
        $result = [];

        if ($mposCredential->getPassword() !== null) {
            $result['password'] = $mposCredential->getPassword();
        }

        if ($mposCredential->getUsername() !== null) {
            $result['username'] = $mposCredential->getUsername();
        }

        if ($mposCredential->getProjectId() !== null) {
            $result['project_id'] = $mposCredential->getProjectId();
        }

        if ($mposCredential->getId() !== null) {
            $result['id'] = $mposCredential->getId();
        }

        return $result;
    }

    /**
     * Decodes MPOS Credential
     *
     *
     * @return MposCredential
     */
    public function decodeMposCredential(array $data)
    {
        return MposCredential::create()
            ->setUsername($data['username'])
            ->setPassword($data['password'])
            ->setProjectId($data['project_id'])
            ->setId($data['id'])
        ;
    }

    public function encodeTransferInput(TransferInput $transferInput)
    {
        return $transferInput->getData();
    }

    /**
     * Decodes Transfer Output
     *
     *
     * @return TransferOutput
     */
    public function decodeTransferOutput(array $data)
    {
        return (new TransferOutput())->setData($data);
    }

    /**
     * Decodes Transfer Output
     *
     * @param array $content
     *
     * @return TransferOutputResult
     */
    public function decodeTransferOutputArray($content)
    {
        return (new TransferOutputResult())->setData($content);
    }

    /**
     * Decodes Sufficient Amount response
     *
     * @param array
     *
     */
    public function decodeSufficientAmountResponse(array $data): SufficientAmountResponse
    {
        $sufficientAmountResponse = new SufficientAmountResponse();

        if (isset($data['is_sufficient'])) {
            $sufficientAmountResponse->setSufficient($data['is_sufficient']);
        }

        return $sufficientAmountResponse;
    }

    /**
     * @return array
     */
    public function encodeSufficientAmountRequest(SufficientAmountRequest $sufficientAmountRequest)
    {
        $result = [];
        if ($sufficientAmountRequest->getAmount() !== null) {
            $result['amount'] = $sufficientAmountRequest->getAmount()->getAmountInCents();
            $result['currency'] = $sufficientAmountRequest->getAmount()->getCurrency();
        }

        return $result;
    }

    /**
     */
    public function encodeTransferConfiguration(TransferConfiguration $transferConfiguration): array
    {
        return (new TransferConfigurationMapper())->mapFromEntity($transferConfiguration);
    }
}
