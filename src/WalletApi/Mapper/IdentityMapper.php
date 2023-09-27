<?php

namespace Paysera\WalletApi\Mapper;

use Paysera\WalletApi\Entity\User\Identity;

class IdentityMapper
{
    /**
     * Maps Identity entity to array
     *
     *
     */
    public function mapFromEntity(Identity $identity): array
    {
        $data = [];
        if ($identity->getName() !== null) {
            $data['name'] = $identity->getName();
        }
        if ($identity->getSurname() !== null) {
            $data['surname'] = $identity->getSurname();
        }
        if ($identity->getNationality() !== null) {
            $data['nationality'] = $identity->getNationality();
        }
        if ($identity->getCode() !== null) {
            $data['code'] = $identity->getCode();
        }

        return $data;
    }

    /**
     * Maps array to Identity entity
     *
     *
     */
    public function mapToEntity(array $data): Identity
    {
        $identity = new Identity();

        if (isset($data['name'])) {
            $identity->setName($data['name']);
        }

        if (isset($data['surname'])) {
            $identity->setSurname($data['surname']);
        }

        if (isset($data['nationality'])) {
            $identity->setNationality($data['nationality']);
        }

        if (isset($data['code'])) {
            $identity->setCode($data['code']);
        }

        return $identity;
    }
}
