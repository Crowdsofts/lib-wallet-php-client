<?php

namespace Paysera\WalletApi\Entity\Location;

class DayWorkingHours
{
    public const DAY_MONDAY = 'monday';
    public const DAY_TUESDAY = 'tuesday';
    public const DAY_WEDNESDAY = 'wednesday';
    public const DAY_THURSDAY = 'thursday';
    public const DAY_FRIDAY = 'friday';
    public const DAY_SATURDAY = 'saturday';
    public const DAY_SUNDAY = 'sunday';

    /**
     * @var int
     */
    protected $day;

    /**
     * @var \Paysera\WalletApi\Entity\Time
     */
    protected $openingTime;

    /**
     * @var \Paysera\WalletApi\Entity\Time
     */
    protected $closingTime;

    /**
     * Set closingTime
     *
     * @param \Paysera\WalletApi\Entity\Time $closingTime
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function setClosingTime($closingTime)
    {
        $this->closingTime = $closingTime;

        return $this;
    }

    /**
     * Get closingTime
     *
     * @return \Paysera\WalletApi\Entity\Time
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }

    /**
     * Set openingTime
     *
     * @param \Paysera\WalletApi\Entity\Time $openingTime
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function setOpeningTime($openingTime)
    {
        $this->openingTime = $openingTime;

        return $this;
    }

    /**
     * Get openingTime
     *
     * @return \Paysera\WalletApi\Entity\Time
     */
    public function getOpeningTime()
    {
        return $this->openingTime;
    }

    /**
     * Get day
     *
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set day
     *
     * @param int $day
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Mark as monday
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function markAsMonday()
    {
        return $this->setDay(self::DAY_MONDAY);
    }

    /**
     * Mark as tuesday
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function markAsTuesday()
    {
        return $this->setDay(self::DAY_TUESDAY);
    }

    /**
     * Mark as wednesday
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function markAsWednesday()
    {
        return $this->setDay(self::DAY_WEDNESDAY);
    }

    /**
     * Mark as thursday
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function markAsThursday()
    {
        return $this->setDay(self::DAY_THURSDAY);
    }

    /**
     * Mark as friday
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function markAsFriday()
    {
        return $this->setDay(self::DAY_FRIDAY);
    }

    /**
     * Mark as saturday
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function markAsSaturday()
    {
        return $this->setDay(self::DAY_SATURDAY);
    }

    /**
     * Mark as sunday
     *
     * @return \Paysera\WalletApi\Entity\Location\DayWorkingHours
     */
    public function markAsSunday()
    {
        return $this->setDay(self::DAY_SUNDAY);
    }

    /**
     * @return string
     */
    public function getFormatted()
    {
        return
            $this->getOpeningTime()->getFormatted() . ' - '
            . (
                $this->getClosingTime()->getHours() === 0 && $this->getClosingTime()->getMinutes() === 0
                ? '24:00'
                : $this->getClosingTime()->getFormatted()
            );
    }

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    public static function create()
    {
        return new static();
    }
}
