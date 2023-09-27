<?php

namespace Paysera\WalletApi\Mapper;

use Paysera\WalletApi\Entity\TransferConfiguration;

class TransferConfigurationMapper
{
    /**
     * Maps TransferConfiguration entity to array
     *
     *
     */
    public function mapFromEntity(TransferConfiguration $transferConfiguration): array
    {
        $data = [];
        if ($transferConfiguration->getClientId() !== null) {
            $data['clientId'] = $transferConfiguration->getClientId();
        }
        if ($transferConfiguration->getApplicationClientId() !== null) {
            $data['appClientId'] = $transferConfiguration->getApplicationClientId();
        }
        if ($transferConfiguration->getWalletId() !== null) {
            $data['walletId'] = $transferConfiguration->getWalletId();
        }

        return $data;
    }

    /**
     * Maps array to TransferConfiguration entity
     *
     *
     */
    public function mapToEntity(array $data): TransferConfiguration
    {
        $transferConfiguration = new TransferConfiguration();

        if (isset($data['clientId'])) {
            $transferConfiguration->setClientId($data['clientId']);
        }

        if (isset($data['appClientId'])) {
            $transferConfiguration->setApplicationClientId($data['appClientId']);
        }

        if (isset($data['walletId'])) {
            $transferConfiguration->setWalletId($data['walletId']);
        }

        return $transferConfiguration;
    }
}
