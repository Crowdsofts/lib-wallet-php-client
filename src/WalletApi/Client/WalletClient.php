<?php

namespace Paysera\WalletApi\Client;

use Paysera\WalletApi\Entity\Allowance;
use Paysera\WalletApi\Entity\Client;
use Paysera\WalletApi\Entity\Location;
use Paysera\WalletApi\Entity\Money;
use Paysera\WalletApi\Entity\MposCredential;
use Paysera\WalletApi\Entity\Payment;
use Paysera\WalletApi\Entity\Project;
use Paysera\WalletApi\Entity\Statement\SearchFilter;
use Paysera\WalletApi\Entity\SufficientAmountRequest;
use Paysera\WalletApi\Entity\Transaction;
use Paysera\WalletApi\Entity\TransferConfiguration;
use Paysera\WalletApi\Entity\TransferFilter;
use Paysera\WalletApi\Entity\TransferInput;
use Paysera\WalletApi\Entity\WalletIdentifier;
use Paysera\WalletApi\Exception\LogicException;
use Paysera\WalletApi\Util\Assert;

class WalletClient extends BaseClient
{
    /**
     * Creates payment using API
     *
     *
     * @return \Paysera\WalletApi\Entity\Payment
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function createPayment(Payment $payment)
    {
        return $this->mapper->decodePayment($this->client->post('payment', $this->mapper->encodePayment($payment)));
    }

    /**
     * Gets payment by ID using API
     *
     * @param integer $paymentId
     *
     * @return \Paysera\WalletApi\Entity\Payment
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getPayment($paymentId)
    {
        Assert::isInt($paymentId);
        $responseData = $this->get('payment/' . $paymentId);

        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Cancels payment by ID using API
     *
     * @param integer $paymentId
     *
     * @return \Paysera\WalletApi\Entity\Payment
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function cancelPayment($paymentId)
    {
        Assert::isInt($paymentId);
        $responseData = $this->delete('payment/' . $paymentId);

        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Removes freeze period for payment
     *
     * @param integer $paymentId
     *
     * @return \Paysera\WalletApi\Entity\Payment
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function removeFreezePeriod($paymentId)
    {
        Assert::isInt($paymentId);
        $responseData = $this->put(
            'payment/' . $paymentId . '/freeze',
            ['freeze_until' => 0],
        );

        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Extends freeze period for payment for specified amount of hours
     *
     * @param integer $paymentId
     * @param integer $periodInHours
     *
     * @return \Paysera\WalletApi\Entity\Payment
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function extendFreezePeriod($paymentId, $periodInHours)
    {
        Assert::isInt($paymentId);
        Assert::isInt($periodInHours);
        $responseData = $this->put(
            'payment/' . $paymentId . '/freeze',
            ['freeze_for' => $periodInHours],
        );

        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Extends freeze period for payment for specified amount of hours
     *
     * @param integer  $paymentId
     *
     * @return \Paysera\WalletApi\Entity\Payment
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function changeFreezePeriod($paymentId, \DateTime  $freezeUntil)
    {
        Assert::isInt($paymentId);
        $responseData = $this->put(
            'payment/' . $paymentId . '/freeze',
            ['freeze_until' => $freezeUntil->getTimestamp()],
        );

        return $this->mapper->decodePayment($responseData);
    }

    /**
     * Finalizes payment, optionally changing the final price
     *
     *
     * @return \Paysera\WalletApi\Entity\Payment
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function finalizePayment(int $paymentId, Money $finalPrice = null)
    {
        Assert::isInt($paymentId);

        $responseData = $this->put(
            'payment/' . $paymentId . '/finalize',
            $finalPrice === null ? null : $this->mapper->encodePrice($finalPrice),
        );

        return $this->mapper->decodePayment($responseData);
    }


    /**
     * Finds payments by provided parameters
     *
     * @param string        $status
     * @param integer       $walletId
     * @param integer       $beneficiaryId
     * @param array $params optional search parameters
     *
     * @return \Paysera\WalletApi\Entity\Search\Result
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function findPayments(
        $status = null,
        $walletId = null,
        $beneficiaryId = null,
        $params = []
    ) {
        Assert::isIntOrNull($walletId);
        Assert::isIntOrNull($beneficiaryId);
        $query = [];
        if ($status !== null) {
            $query['status'] = $status;
        }
        if ($walletId !== null) {
            $query['wallet'] = $walletId;
        }
        if ($beneficiaryId !== null) {
            $query['beneficiary'] = $beneficiaryId;
        }
        if (count($params)) {
            $query = array_merge($query, $params);
        }
        $result = $this->get('payments' . (count($query) > 0 ? '?' . http_build_query($query) : ''));

        return $this->mapper->decodePaymentSearchResult($result);
    }

    /**
     * Creates allowance using API
     *
     *
     * @throws ApiException
     */
    public function createAllowance(Allowance $allowance): Allowance
    {
        $requestData = $this->mapper->encodeAllowance($allowance);
        $responseData = $this->post('allowance', $requestData);

        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Gets allowance by ID using API
     *
     * @param integer $allowanceId
     *
     * @return Allowance
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getAllowance($allowanceId)
    {
        Assert::isInt($allowanceId);
        $responseData = $this->get('allowance/' . $allowanceId);

        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Gets active allowance for specified wallet using API
     *
     * @param integer $walletId
     *
     * @return Allowance
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getAllowanceForWallet($walletId)
    {
        Assert::isId($walletId);
        $responseData = $this->get('allowance/active/' . $walletId);

        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Gets current allowance limit for specified wallet using API
     *
     * @param integer $walletId
     * @param string  $currency
     *
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getAllowanceLimit($walletId, $currency = 'EUR')
    {
        Assert::isId($walletId);
        Assert::isScalar($currency);
        $responseData = $this->get('allowance/limit/' . $walletId . '?currency=' . urlencode($currency));

        return $this->mapper->decodeMoney($responseData);
    }

    /**
     * Cancels allowance by ID using API
     *
     * @param integer $allowanceId
     *
     * @return Allowance
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function cancelAllowance($allowanceId)
    {
        Assert::isInt($allowanceId);
        $responseData = $this->delete('allowance/' . $allowanceId);

        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Cancels allowance for specified wallet using API
     *
     * @param integer $walletId
     *
     * @return Allowance
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function cancelAllowanceForWallet($walletId)
    {
        Assert::isId($walletId);
        $responseData = $this->delete('allowance/active/' . $walletId);

        return $this->mapper->decodeAllowance($responseData);
    }

    /**
     * Creates transaction using API
     *
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function createTransaction(Transaction $transaction)
    {
        $requestData = $this->mapper->encodeTransaction($transaction);
        $responseData = $this->post('transaction', $requestData);

        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Gets transaction by transaction key using API
     *
     * @param string $transactionKey
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getTransaction($transactionKey)
    {
        Assert::isScalar($transactionKey);
        $responseData = $this->get('transaction/' . $transactionKey);

        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Revokes transaction by transaction key using API
     *
     * @param string $transactionKey
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function revokeTransaction($transactionKey)
    {
        Assert::isScalar($transactionKey);
        $responseData = $this->delete('transaction/' . $transactionKey);

        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Confirms transaction by transaction key using API
     *
     * @param string $transactionKey
     * @param array $transactionPrices
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function confirmTransaction($transactionKey, $transactionPrices = [])
    {
        Assert::isScalar($transactionKey);
        $requestData = $this->mapper->encodeTransactionPrices($transactionPrices);
        $responseData = $this->put('transaction/' . $transactionKey . '/confirm', $requestData);

        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Tries to accept transaction by active allowance using API
     *
     * @param string $transactionKey
     * @param int|\Paysera\WalletApi\Entity\WalletIdentifier|string $payer
     * @param \Paysera\WalletApi\Entity\FundsSource[] $fundsSources
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     *
     */
    public function acceptTransactionUsingAllowance($transactionKey, $payer, $fundsSources = [])
    {
        Assert::isScalar($transactionKey);

        $content = [];

        if (count($fundsSources) > 0) {
            $content = array_merge($this->mapper->encodeFundsSources($fundsSources));
        }

        if ($payer instanceof WalletIdentifier) {
            $payer->validate();

            $content = array_merge($content, $this->mapper->encodePayer($payer));
            $uri = 'transaction/' . $transactionKey . '/reserve';
        } else {
            Assert::isId($payer);

            $uri = 'transaction/' . $transactionKey . '/reserve/' . (string)$payer;
        }

        $responseData = $this->put($uri, $content);

        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Tries to accept transaction by sending user's PIN code using API
     *
     * @param string $transactionKey
     * @param int|\Paysera\WalletApi\Entity\WalletIdentifier|string $payer
     * @param string $pin
     * @param \Paysera\WalletApi\Entity\FundsSource[] $fundsSources
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     *
     */
    public function acceptTransactionUsingPin($transactionKey, $payer, $pin, $fundsSources = [])
    {
        Assert::isScalar($transactionKey);
        Assert::isScalar($pin);

        $content = $this->mapper->encodePin($pin);

        if (count($fundsSources) > 0) {
            $content = array_merge($this->mapper->encodeFundsSources($fundsSources));
        }

        if ($payer instanceof WalletIdentifier) {
            $payer->validate();

            $content = array_merge($content, $this->mapper->encodePayer($payer));
            $uri = 'transaction/' . $transactionKey . '/reserve';
        } else {
            Assert::isId($payer);

            $uri = 'transaction/' . $transactionKey . '/reserve/' . (string)$payer;
        }

        $responseData = $this->put($uri, $content);

        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * Sends FLASH SMS using API to the user to accept transaction
     *
     * @param string  $transactionKey
     * @param integer $walletId
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function sendTransactionFlashSms($transactionKey, $walletId)
    {
        Assert::isScalar($transactionKey);
        Assert::isId($walletId);
        $responseData = $this->put('transaction/' . $transactionKey . '/flash/' . $walletId);

        return $this->mapper->decodeTransaction($responseData);
    }

    /**
     * @param string $transactionKey
     *
     * @return \Paysera\WalletApi\Entity\Inquiry\InquiryResult[]
     *
     * @throws LogicException
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getInquiredInformation($transactionKey)
    {
        Assert::isScalar($transactionKey);
        $responseData = $this->get('transaction/' . $transactionKey . '/inquired-information');

        return $this->mapper->decodeInquiryResults($responseData);
    }

    /**
     * Gets available types to accept transaction using API
     *
     * @param string  $transactionKey
     * @param integer $walletId
     *
     * @return string[]
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getAvailableTransactionTypes($transactionKey, $walletId)
    {
        Assert::isScalar($transactionKey);
        Assert::isId($walletId);

        return $this->get('transaction/' . $transactionKey . '/type/' . $walletId);
    }

    /**
     * Gets wallet by id using API
     *
     * @param integer $walletId
     *
     * @return \Paysera\WalletApi\Entity\Wallet
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getWallet($walletId)
    {
        Assert::isId($walletId);
        $responseData = $this->get('wallet/' . $walletId);

        return $this->mapper->decodeWallet($responseData);
    }

    /**
     * Gets wallet balance by id using API
     *
     * @param integer $walletId
     *
     * @return \Paysera\WalletApi\Entity\Wallet\Balance
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getWalletBalance($walletId)
    {
        Assert::isId($walletId);
        $responseData = $this->get('wallet/' . $walletId . '/balance');

        return $this->mapper->decodeWalletBalance($responseData);
    }

    /**
     * Gets statements for wallet by id using API
     *
     * @param integer                                   $walletId
     *
     * @return \Paysera\WalletApi\Entity\Search\Result|\Paysera\WalletApi\Entity\Statement[]
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getWalletStatements($walletId, SearchFilter $filter = null)
    {
        Assert::isId($walletId);
        if ($filter !== null) {
            $query = '?' . http_build_query($this->mapper->encodeStatementFilter($filter), null, '&');
        } else {
            $query = '';
        }

        return $this->mapper->decodeStatementSearchResult(
            $this->get('wallet/' . $walletId . '/statements' . $query),
        );
    }

    /**
     * Gets wallet by search parameters
     *
     *
     * @return \Paysera\WalletApi\Entity\Wallet
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getWalletBy(array $parameters)
    {
        $responseData = $this->get('wallet?' . http_build_query($parameters, null, '&'));

        return $this->mapper->decodeWallet($responseData);
    }

    /**
     * Gets wallets by contact list (emails or phone numbers)
     *
     * @param bool  $private  whether to send hashes of contacts to avoid sending private information
     *
     * @return \Paysera\WalletApi\Entity\Wallet[] array keys are provided contacts (only the found ones are provided)
     */
    public function getWalletsByContacts(array $contacts, $private = false)
    {
        if (count($contacts) === 0) {
            return [];
        }

        $map = [];
        $email = [];
        $phone = [];
        foreach ($contacts as $contact) {
            if (str_contains($contact, '@')) {
                $formatted = strtolower($contact);
                if ($private) {
                    $formatted = sha1($formatted);
                }
                $email[] = $formatted;
            } else {
                $formatted = preg_replace('/[^\d]/', '', $contact);
                if ($private) {
                    $formatted = sha1($formatted);
                }
                $phone[] = $formatted;
            }
            $map[$formatted] = $contact;
        }
        $parameters = [];
        if (count($email) > 0) {
            $parameters[$private ? 'email_hash' : 'email'] = implode(',', $email);
        }
        if (count($phone) > 0) {
            $parameters[$private ? 'phone_hash' : 'phone'] = implode(',', $phone);
        }
        $responseData = $this->get('wallets?' . http_build_query($parameters, null, '&'));

        $result = [];
        foreach ($responseData as $key => $walletData) {
            $result[$map[$key]] = $this->mapper->decodeWallet($walletData);
        }

        return $result;
    }

    /**
     * Gets user by id using API
     *
     * @param integer $userId
     *
     * @return \Paysera\WalletApi\Entity\User
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getUser($userId)
    {
        Assert::isId($userId);
        $responseData = $this->get('user/' . $userId);

        return $this->mapper->decodeUser($responseData);
    }

    /**
     * Gets user's email by id using API
     *
     * @param integer $userId
     *
     * @return string
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getUserEmail($userId)
    {
        Assert::isId($userId);

        return $this->get('user/' . $userId . '/email');
    }

    /**
     * Gets user's phone by id using API
     *
     * @param integer $userId
     *
     * @return string
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getUserPhone($userId)
    {
        Assert::isId($userId);

        return $this->get('user/' . $userId . '/phone');
    }

    /**
     * Gets user's confirmed phone numbers by id using API
     *
     * @param integer $userId
     *
     * @return array
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getUserConfirmedPhoneNumbers($userId)
    {
        Assert::isId($userId);

        return $this->get('user/' . $userId . '/confirmed-phones');
    }

    /**
     * Gets wallet barcode by search parameters
     *
     *
     * @return string
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getWalletBarcodeBy(array $parameters)
    {
        $responseData = $this->get('wallet/barcode?' . http_build_query($parameters, null, '&'));

        return $responseData['barcode'];
    }

    /**
     * Gets user's address by id using API
     *
     * @param integer $userId
     *
     * @return \Paysera\WalletApi\Entity\User\Address
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getUserAddress($userId)
    {
        Assert::isId($userId);
        $responseData = $this->get('user/' . $userId . '/address');

        return $this->mapper->decodeAddress($responseData);
    }

    /**
     * Gets user's identity by id using API
     *
     * @param integer $userId
     *
     * @return \Paysera\WalletApi\Entity\User\Identity
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getUserIdentity($userId)
    {
        Assert::isId($userId);
        $responseData = $this->get('user/' . $userId . '/identity');

        return $this->mapper->decodeIdentity($responseData);
    }

    /**
     * Gets user's wallets by id using API
     *
     * @param integer $userId
     *
     * @return \Paysera\WalletApi\Entity\Wallet[]
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getUserWallets($userId)
    {
        Assert::isId($userId);
        $responseData = $this->get('user/' . $userId . '/wallets');

        return $this->mapper->decodeWallets($responseData);
    }

    /**
     * Get project by id
     *
     * @param int $projectId
     *
     * @return \Paysera\WalletApi\Entity\Project
     */
    public function getProject($projectId)
    {
        $responseData = $this->get('project/' . $projectId);

        return $this->mapper->decodeProject($responseData);
    }

    /**
     * Creates project
     *
     *
     * @return \Paysera\WalletApi\Entity\Project
     *
     * @throws LogicException
     */
    public function saveProject(Project $project)
    {
        if ($project->getId() === null) {
            throw new LogicException("Project must have id property set");
        }

        $responseData = $this->put('project/' . $project->getId(), $this->mapper->encodeProject($project));

        return $this->mapper->decodeProject($responseData);
    }

    /**
     * Creates location
     *
     *
     * @return Location
     */
    public function createLocation(int $projectId, Location $location)
    {
        $responseData = $this->post('project/' . $projectId . '/location', $this->mapper->encodeLocation($location));

        return $this->mapper->decodeLocation($responseData);
    }

    /**
     * Updates location
     *
     *
     * @throws LogicException
     */
    public function updateLocation(Location $location): Location
    {
        if ($location->getId() === null) {
            throw new LogicException("Location id has been not provided");
        }

        $responseData = $this->put('location/' . $location->getId(), $this->mapper->encodeLocation($location));

        return $this->mapper->decodeLocation($responseData);
    }

    /**
     * Get project locations
     *
     * @param int $projectId
     *
     * @return Location[]
     */
    public function getProjectLocations(
        $projectId,
        Location_SearchFilter $filter = null
    ) {
        if ($filter !== null) {
            $query = '?' . http_build_query($this->mapper->encodeLocationFilter($filter), null, '&');
        } else {
            $query = '';
        }

        $responseData = $this->get('project/' . $projectId . '/locations' . $query);

        $locations = [];
        foreach ($responseData as $item) {
            $locations[] = $this->mapper->decodeLocation($item);
        }

        return $locations;
    }

    /**
     * Get all locations
     *
     * @return \Paysera\WalletApi\Entity\Search\Result|Location[]
     */
    public function getLocations(Location_SearchFilter $filter = null)
    {
        if ($filter !== null) {
            $query = '?' . http_build_query($this->mapper->encodeLocationFilter($filter), null, '&');
        } else {
            $query = '';
        }

        return $this->mapper->decodeLocationSearchResult(
            $this->get('locations' . $query),
        );
    }

    /**
     * Get Location pay categories
     *
     * @return \Paysera\WalletApi\Entity\PayCategory[]
     */
    public function getLocationPayCategories($locale)
    {
        $query = '?' . http_build_query(['locale' => $locale], null, '&');

        return $this->mapper->decodeLocationPayCategories(
            $this->get('locations/pay-categories' . $query),
        );
    }

    /**
     * If clientId is not provided will return current client
     *
     * @param null|int $clientId
     *
     * @return Client
     */
    public function getClient($clientId = null)
    {
        $path = $clientId === null ? 'client' : 'client/' . $clientId;
        $responseData = $this->get($path);

        return $this->mapper->decodeClient($responseData);
    }

    /**
     * Gets Clients by specified Filter
     *
     *
     * @return Client[]
     */
    public function getClients(\Paysera\WalletApi\Entity\Client\SearchFilter $filter)
    {
        $query = '?' . http_build_query($this->mapper->encodeClientFilter($filter), null, '&');

        return $this->mapper->decodeClientSearchResult(
            $this->get('clients' . $query),
        );
    }

    /**
     * Create client
     *
     *
     * @return Client
     */
    public function createClient(Client $client)
    {
        $responseData = $this->post('client', $this->mapper->encodeClient($client));

        return $this->mapper->decodeClient($responseData);
    }

    /**
     * Update client
     *
     *
     * @return Client
     */
    public function updateClient(Client $client)
    {
        $responseData = $this->put('client/' . $client->getId(), $this->mapper->encodeClient($client));

        return $this->mapper->decodeClient($responseData);
    }

    /**
     * Create MPOS Credential
     *
     *
     * @return \Paysera\WalletApi\Entity\MposCredential
     */
    public function createMposCredential(MposCredential $mposCredential)
    {
        $responseData = $this->post('project/mpos-credentials', $this->mapper->encodeMposCredential($mposCredential));

        return $this->mapper->decodeMposCredential($responseData);
    }

    /**
     * Get MPOS Credential
     *
     * @param int $mposCredentialId
     *
     * @return \Paysera\WalletApi\Entity\MposCredential
     */
    public function getMposCredential($mposCredentialId)
    {
        $responseData = $this->get(sprintf('project/mpos-credentials/%s', urlencode($mposCredentialId)));

        return $this->mapper->decodeMposCredential($responseData);
    }

    /**
     * Get Transfer
     *
     * @param int $transferId
     *
     * @return \Paysera\WalletApi\Entity\TransferOutput $transferId
     */
    public function getTransfer($transferId)
    {
        $responseData = $this->get(sprintf('payment-initiation/transfers/%s', urlencode($transferId)));

        return $this->mapper->decodeTransferOutput($responseData);
    }

    /**
     * Get Transfers
     *
     *
     * @return \Paysera\WalletApi\Entity\TransferOutputResult $transferId
     */
    public function getTransfers(TransferFilter $filter)
    {
        $query = http_build_query(
            $this->mapper->encodeFilter($filter),
            null,
            '&',
        );

        $responseData = $this->get(sprintf(
            'payment-initiation/transfers%s',
            $query !== '' ? '?' . $query : '',
        ));

        return $this->mapper->decodeTransferOutputArray($responseData);
    }

    /**
     * Create Transfer
     *
     *
     * @return \Paysera\WalletApi\Entity\TransferOutput
     */
    public function createTransfer(TransferInput $transferInput)
    {
        $responseData = $this->post(
            sprintf('payment-initiation/transfers'),
            $this->mapper->encodeTransferInput($transferInput),
        );

        return $this->mapper->decodeTransferOutput($responseData);
    }

    /**
     * Confirm account balance is sufficient
     *
     *
     * @return \Paysera\WalletApi\Entity\SufficientAmountResponse
     */
    public function hasSufficientAmount(
        string $walletId,
        SufficientAmountRequest $sufficientAmountRequest
    ) {
        $query = '?' . http_build_query(
            $this->mapper->encodeSufficientAmountRequest($sufficientAmountRequest),
            null,
            '&',
        );

        $responseData = $this->get(sprintf('wallet/%s/sufficient-amount', $walletId) . $query);

        return $this->mapper->decodeSufficientAmountResponse($responseData);
    }

    public function getPes($userId)
    {
        $responseData = $this->get(sprintf('user/%s/pes', $userId));

        return $this->mapper->decodePes($responseData);
    }

    /**
     */
    public function createTransferConfiguration(TransferConfiguration $transferConfiguration): Client
    {
        $responseData = $this->post(
            'client/configuration/transfer',
            $this->mapper->encodeTransferConfiguration($transferConfiguration),
        );

        return $this->mapper->decodeClient($responseData);
    }

    /**
     * @param int $clientId
     *
     * @return Client
     */
    public function deleteTransferConfiguration($clientId)
    {
        $responseData = $this->delete(sprintf('client/%s/configuration/transfer', $clientId));

        return $this->mapper->decodeClient($responseData);
    }
}
