<?php

namespace Paysera\WalletApi\Mapper;

use Paysera\WalletApi\Entity\Inquiry\InquiryResult;

class InquiryResultMapper
{
    public function __construct(private array $inquiryResultMapper)
    {
    }

    /**
     * Maps array to Inquiry result entity
     *
     *
     */
    public function mapToEntity(array $data): InquiryResult
    {
        $inquiryResult = new InquiryResult();

        if (isset($data['inquiry_identifier'])) {
            $inquiryResult->setInquiryIdentifier($data['inquiry_identifier']);
        }

        if (isset($data['item_identifier'])) {
            $inquiryResult->setItemIdentifier($data['item_identifier']);
        }

        if (isset($data['value'])) {
            $mapper = $this->getInquiryResultMapper($data['item_type']);
            $inquiryResult->setValue($mapper->mapToEntity($data['value']));
        }

        return $inquiryResult;
    }

    /**
     * Maps Inquiry result entity to array
     *
     *
     */
    public function mapFromEntity(InquiryResult $entity): array
    {
        $mapper = $this->getInquiryResultMapper($entity->getItemType());

        return ['inquiry_identifier' => $entity->getInquiryIdentifier(), 'item_identifier' => $entity->getItemIdentifier(), 'value' => $mapper->mapFromEntity(
            $entity->getValue(),
        )];
    }

    /**
     * @param string $type
     *
     */
    private function getInquiryResultMapper($type): object
    {
        return $this->inquiryResultMapper[$type];
    }
}
