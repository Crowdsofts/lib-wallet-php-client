<?php

namespace App\Test\Paysera\WalletApi;

use Paysera\WalletApi\Entity\Location\SearchFilter;
use Paysera\WalletApi\Entity\Transaction;
use Paysera\WalletApi\Entity\User\Identity;
use Paysera\WalletApi\Mapper;
use Paysera\WalletApi\Mapper\IdentityMapper;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    public function testMapperJoinsLocationSearchFilterStatusesArray()
    {
        $filter = new SearchFilter();
        $filter->setStatuses(['a', 'b']);

        $mapper = new Mapper();
        $encoded = $mapper->encodeLocationFilter($filter);

        $statuses = explode(',', $encoded['status']);
        $this->assertCount(2, $statuses);
        $this->assertContains('a', $statuses);
        $this->assertContains('b', $statuses);
    }

    public function testIdentityMapperEncoding()
    {
        $identity = new Identity();
        $identity
            ->setName('Name')
            ->setSurname("Surname")
            ->setCode(9999999)
            ->setNationality("LT")
        ;

        $mapper = new IdentityMapper();
        $result = $mapper->mapFromEntity($identity);

        $this->assertSame($identity->getName(), $result['name']);
        $this->assertSame($identity->getSurname(), $result['surname']);
        $this->assertSame($identity->getCode(), $result['code']);
        $this->assertSame($identity->getNationality(), $result['nationality']);
    }

    public function testIdentityMapperDecoding()
    {
        $identity = [
            'name' => 'Name',
            'surname' => 'Surname',
            'code' => 9999999,
            'nationality' => 'LT',
        ];

        $mapper = new IdentityMapper();
        $result = $mapper->mapToEntity($identity);

        $this->assertSame($identity['name'], $result->getName());
        $this->assertSame($identity['surname'], $result->getSurname());
        $this->assertSame($identity['code'], $result->getCode());
        $this->assertSame($identity['nationality'], $result->getNationality());
    }

    public function testDecodesTransactionWithReserveUntil()
    {
        $until = new \DateTime('+1 day');
        $data = [
            'transaction_key' => 'abc',
            'created_at' => (new \DateTime('-1 day'))->getTimestamp(),
            'status' => Transaction::STATUS_NEW,
            'reserve' => [
                'until' => $until->getTimestamp(),
            ],
        ];

        $mapper = new Mapper();
        $transaction = $mapper->decodeTransaction($data);

        $this->assertEquals($until->getTimestamp(), $transaction->getReserveUntil()->getTimestamp());
    }

    public function testDecodesTransactionWithReserveFor()
    {
        $for = 10;
        $data = [
            'transaction_key' => 'abc',
            'created_at' => (new \DateTime('-1 day'))->getTimestamp(),
            'status' => Transaction::STATUS_NEW,
            'reserve' => [
                'for' => $for,
            ],
        ];

        $mapper = new Mapper();
        $transaction = $mapper->decodeTransaction($data);

        $this->assertEquals($for, $transaction->getReserveFor());
    }

    public function testDecodesPep()
    {
        $data = [
            'name' => 'nameValue',
            'relation' => 'relationValue',
            'positions' => [
                'positionAValue',
            ],
        ];

        $mapper = new Mapper();
        $pepObj = $mapper->decodePep($data);
        self::assertEquals('nameValue', $pepObj->getName());
        self::assertEquals('relationValue', $pepObj->getRelation());
        self::assertEquals('positionAValue', $pepObj->getPositions()[0]);
    }
}
