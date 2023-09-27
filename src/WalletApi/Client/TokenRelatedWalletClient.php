<?php

namespace Paysera\WalletApi\Client;

use Paysera\WalletApi\Entity\Search\Result;
use Paysera\WalletApi\Entity\Statement;
use Paysera\WalletApi\Entity\Statement\SearchFilter;

class TokenRelatedWalletClient extends WalletClient
{
    /**
     * @var \Paysera\WalletApi\Entity\MacAccessToken
     */
    protected $currentAccessToken;

    /**
     * Gets active allowance for current wallet
     *
     * @return \Paysera\WalletApi\Entity\Allowance
     *
     * @throws \Paysera\WalletApi\Exception\ApiException
     */
    public function getActiveAllowance()
    {
        return $this->mapper->decodeAllowance($this->get('allowance/active/me'));
    }

    /**
     * @param string $currency
     *
     * @return \Paysera\WalletApi\Entity\Money
     */
    public function getActiveAllowanceLimit($currency = 'EUR')
    {
        return parent::getAllowanceLimit('me', $currency);
    }

    /**
     * Tries to accept transaction by sending user's PIN code using API
     *
     * @param string                                 $transactionKey
     * @param string                                 $pin
     * @param \Paysera\WalletApi\Entity\FundsSource[] $fundsSources
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     *
     */
    public function acceptTransactionUsingCurrentPin($transactionKey, $pin, $fundsSources = [])
    {
        return $this->acceptTransactionUsingPin($transactionKey, 'me', $pin, $fundsSources);
    }

    /**
     * Gets statements for current wallet using API
     *
     *
     * @return Result|Statement[]
     */
    public function getCurrentWalletStatements(SearchFilter $filter = null): Result|array
    {
        return $this->getWalletStatements('me', $filter);
    }

    /**
     * Tries to accept transaction by active allowance using API
     *
     * @param string                                               $transactionKey
     * @param int|\Paysera\WalletApi\Entity\WalletIdentifier|string $payer
     * @param \Paysera\WalletApi\Entity\FundsSource[]               $fundsSources
     *
     * @return \Paysera\WalletApi\Entity\Transaction
     */
    public function acceptTransactionUsingAllowance($transactionKey, $payer = 'me', $fundsSources = [])
    {
        return parent::acceptTransactionUsingAllowance($transactionKey, $payer, $fundsSources);
    }

    public function getAllowanceForWallet($walletId = 'me')
    {
        return parent::getAllowanceForWallet($walletId);
    }

    public function cancelAllowanceForWallet($walletId = 'me')
    {
        return parent::cancelAllowanceForWallet($walletId);
    }

    public function sendTransactionFlashSms($transactionKey, $walletId = 'me')
    {
        return parent::sendTransactionFlashSms($transactionKey, $walletId);
    }

    public function getAvailableTransactionTypes($transactionKey, $walletId = 'me')
    {
        return parent::getAvailableTransactionTypes($transactionKey, $walletId);
    }

    public function getWallet($walletId = 'me')
    {
        return parent::getWallet($walletId);
    }

    public function getWalletBalance($walletId = 'me')
    {
        return parent::getWalletBalance($walletId);
    }

    public function getUser($userId = 'me')
    {
        return parent::getUser($userId);
    }

    public function getUserEmail($userId = 'me')
    {
        return parent::getUserEmail($userId);
    }

    public function getUserPhone($userId = 'me')
    {
        return parent::getUserPhone($userId);
    }

    public function getUserConfirmedPhoneNumbers($userId = 'me')
    {
        return parent::getUserConfirmedPhoneNumbers($userId);
    }

    public function getUserAddress($userId = 'me')
    {
        return parent::getUserAddress($userId);
    }

    public function getUserIdentity($userId = 'me')
    {
        return parent::getUserIdentity($userId);
    }

    public function getUserWallets($userId = 'me')
    {
        return parent::getUserWallets($userId);
    }

    public function getPes($userId = 'me')
    {
        return parent::getPes($userId);
    }

    /**
     * Gets currentAccessToken
     *
     * @return \Paysera\WalletApi\Entity\MacAccessToken
     */
    public function getCurrentAccessToken()
    {
        return $this->currentAccessToken;
    }

    /**
     * Only for internal use - will not change the token with which requests are made
     *
     * @param \Paysera\WalletApi\Entity\MacAccessToken $currentAccessToken
     */
    public function setCurrentAccessToken($currentAccessToken)
    {
        $this->currentAccessToken = $currentAccessToken;
    }
}
