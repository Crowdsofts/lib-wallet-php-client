<?php

namespace App\Test\Paysera\WalletApi\Mappers;

use Paysera\WalletApi\Entity\Inquiry\InquiryItem;
use Paysera\WalletApi\Mapper\IdentityMapper;
use Paysera\WalletApi\Mapper\InquiryResultMapper;
use Paysera\WalletApi\Mapper\PlainValueMapper;
use PHPUnit\Framework\TestCase;

class InquiryResultMapperTest extends TestCase
{
    private InquiryResultMapper $inquiryResultMapper;

    protected function setUp(): void
    {
        $this->inquiryResultMapper = new InquiryResultMapper([
            InquiryItem::TYPE_USER_IDENTITY =>
                new IdentityMapper(),
            InquiryItem::TYPE_PERSON_CODE =>
                new PlainValueMapper(),
        ]);
    }

    public function testInquiryResultValueWithoutIdentity()
    {
        $inquiryValue = 9999999;

        $data = [
            'inquiry_identifier' => 'identifier',
            'item_identifier' => 'item identifier',
            'item_type' => 'person_code',
            'value' => $inquiryValue,
        ];

        $result = $this->inquiryResultMapper->mapToEntity($data);

        $this->assertSame($result->getInquiryIdentifier(), $data['inquiry_identifier']);
        $this->assertSame($result->getItemIdentifier(), $data['item_identifier']);
        $this->assertNotNull($result->getValue());
        $this->assertSame($result->getValue(), $inquiryValue);
    }

    public function testInquiryResultValueWithIdentity()
    {
        $inquiryValue = [
            'name' => 'Name',
            'surname' => 'Surname',
            'nationality' => 'LT',
            'code' => 606060,
        ];

        $data = [
            'inquiry_identifier' => 'identifier',
            'item_identifier' => 'item identifier',
            'item_type' => 'user_identity',
            'value' => $inquiryValue,
        ];

        $result = $this->inquiryResultMapper->mapToEntity($data);

        $this->assertSame($result->getInquiryIdentifier(), $data['inquiry_identifier']);
        $this->assertSame($result->getItemIdentifier(), $data['item_identifier']);
        $this->assertNotNull($result->getValue());

        $identity = $result->getValue();
        $this->assertInstanceOf('\Paysera\WalletApi\Entity\User\Identity', $identity);
        $this->assertSame($identity->getName(), $inquiryValue['name']);
        $this->assertSame($identity->getSurname(), $inquiryValue['surname']);
        $this->assertSame($identity->getNationality(), $inquiryValue['nationality']);
        $this->assertSame($identity->getCode(), $inquiryValue['code']);
    }
}
