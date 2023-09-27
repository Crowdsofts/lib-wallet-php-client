<?php

namespace Paysera\WalletApi\Mapper;

class PlainValueMapper
{
    /**
     * Returns same data
     *
     *
     */
    public function mapFromEntity(mixed $data)
    {
        return $data;
    }

    /**
     * Returns same data
     *
     *
     */
    public function mapToEntity(mixed $entity)
    {
        return $entity;
    }
}
