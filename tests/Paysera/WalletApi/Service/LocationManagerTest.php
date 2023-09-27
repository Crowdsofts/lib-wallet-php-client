<?php

namespace App\Test\Paysera\WalletApi\Service;

use Paysera\WalletApi\Entity\Location;
use Paysera\WalletApi\Entity\Location\DayWorkingHours;
use Paysera\WalletApi\Entity\Time;
use Paysera\WalletApi\Service\LocationManager;
use PHPUnit\Framework\TestCase;

/**
 * Created by: Gediminas Samulis
 * Date: 2014-02-25
 */

class LocationManagerTest extends TestCase
{
    protected LocationManager $service;

    protected function setUp(): void
    {
        $this->service = new LocationManager();
    }

    /**
     * @dataProvider dataProviderForTestIsLocationOpen1
     */
    public function testIsLocationOpen_working_hours_exist($expected, $date)
    {
        $location = new Location();

        $workingHours1 = new DayWorkingHours();
        $workingHours1->setDay(DayWorkingHours::DAY_MONDAY);
        $workingHours1->setOpeningTime(new Time(9, 0));
        $workingHours1->setClosingTime(new Time(18, 0));

        $workingHours2 = new DayWorkingHours();
        $workingHours2->setDay(DayWorkingHours::DAY_TUESDAY);
        $workingHours2->setOpeningTime(new Time(9, 0));
        $workingHours2->setClosingTime(new Time(18, 0));

        $workingHours4 = new DayWorkingHours();
        $workingHours4->setDay(DayWorkingHours::DAY_THURSDAY);
        $workingHours4->setOpeningTime(new Time(9, 0));
        $workingHours4->setClosingTime(new Time(18, 0));

        $workingHours5 = new DayWorkingHours();
        $workingHours5->setDay(DayWorkingHours::DAY_FRIDAY);
        $workingHours5->setOpeningTime(new Time(22, 0));
        $workingHours5->setClosingTime(new Time(3, 0));

        $workingHours7 = new DayWorkingHours();
        $workingHours7->setDay(DayWorkingHours::DAY_SUNDAY);
        $workingHours7->setOpeningTime(new Time(0, 0));
        $workingHours7->setClosingTime(new Time(0, 0));

        $location->setWorkingHours(
            [$workingHours1, $workingHours2, $workingHours4, $workingHours5, $workingHours7],
        );

        $this->assertEquals($expected, $this->service->isLocationOpen($location, $date));
    }

    public function testIsLocationClosed_working_hours_empty()
    {
        $location = new Location();

        //working days not defined at all:
        $location->setWorkingHours([]);
        //any day, any time:
        $this->assertFalse($this->service->isLocationOpen($location, new \DateTime('2014-03-01 10:00')));
    }

    public static function dataProviderForTestIsLocationOpen1(): array
    {
        return [
            //monday:
            [true, new \DateTime('2014-02-24 10:00')],
            [true, new \DateTime('2014-02-24 17:00')],
            [true, new \DateTime('2014-02-24 18:00')],
            [false, new \DateTime('2014-02-24 08:00')],
            [false, new \DateTime('2014-02-24 08:59')],
            [false, new \DateTime('2014-02-24 18:01')],
            //wednesday:
            [false, new \DateTime('2014-02-26 02:00')],
            [false, new \DateTime('2014-02-26 10:00')],
            [false, new \DateTime('2014-02-26 17:00')],
            //friday:
            [true, new \DateTime('2014-02-28 23:00')],
            [true, new \DateTime('2014-03-01 01:00')],
            [true, new \DateTime('2014-03-01 03:00')],
            [false, new \DateTime('2014-02-28 21:00')],
            [false, new \DateTime('2014-03-01 04:00')],
            //saturday (not defined day)
            [false, new \DateTime('2014-03-01 10:00')],
            //sunday:
            [false, new \DateTime('2014-03-01 23:59')],
            [true, new \DateTime('2014-03-02 00:00')],
            [true, new \DateTime('2014-03-02 10:00')],
            [true, new \DateTime('2014-03-03 00:00')],
            [false, new \DateTime('2014-03-03 01:00')],
        ];
    }
}
