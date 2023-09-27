<?php

namespace Paysera\WalletApi\Entity\Inquiry;

class InquiryResult
{
    /**
     * @var string
     */
    private $inquiryIdentifier;

    /**
     * @var string
     */
    private $itemIdentifier;

    /**
     */
    private $value;

    /**
     * @return string
     */
    public function getInquiryIdentifier()
    {
        return $this->inquiryIdentifier;
    }

    /**
     * @param string $inquiryIdentifier
     *
     * @return $this
     */
    public function setInquiryIdentifier($inquiryIdentifier)
    {
        $this->inquiryIdentifier = $inquiryIdentifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemIdentifier()
    {
        return $this->itemIdentifier;
    }

    /**
     * @param string $itemIdentifier
     *
     * @return $this
     */
    public function setItemIdentifier($itemIdentifier)
    {
        $this->itemIdentifier = $itemIdentifier;

        return $this;
    }

    /**
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return $this
     */
    public function setValue(mixed $value)
    {
        $this->value = $value;

        return $this;
    }
}
