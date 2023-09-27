<?php

namespace Paysera\WalletApi\Entity;

use Paysera\WalletApi\Entity\Location\DayWorkingHours;
use Paysera\WalletApi\Entity\Location\Price;

/**
 * Entity representing Location
 */
class Location
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const SERVICE_TYPE_CASH_IN = 'cash_in';
    public const SERVICE_TYPE_CASH_OUT = 'cash_out';
    public const SERVICE_TYPE_IDENTIFICATION = 'identification';
    public const SERVICE_TYPE_PAY = 'pay';

    private static array $serviceTypes = [
        self::SERVICE_TYPE_CASH_IN,
        self::SERVICE_TYPE_CASH_OUT,
        self::SERVICE_TYPE_IDENTIFICATION,
        self::SERVICE_TYPE_PAY,
    ];

    /**
     * @readonly
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $address;

    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lng;

    private int $radius = 0;

    /**
     * @var \Paysera\WalletApi\Entity\Location\Price[]
     */
    private array $prices = [];

    /**
     * @var \Paysera\WalletApi\Entity\Location\DayWorkingHours[]
     */
    private array $workingHours = [];

    /**
     * @var string
     */
    private $imagePinOpen;

    /**
     * @var string
     */
    private $imagePinClosed;

    private array $services = [];

    private array $payCategories = [];

    private array $cashInTypes = [];

    private array $cashOutTypes = [];

    /**
     * @var string
     */
    private $status;

    /**
     * @var bool
     */
    private $public;

    /**
     * Set id
     *
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setId(mixed $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set lat
     *
     * @param float $lat
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set radius
     *
     * @param int $radius
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setRadius($radius)
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * Get radius
     *
     * @return int
     */
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Adds price
     *
     *
     * @return Location
     */
    public function addPrice(Price $price)
    {
        $this->prices[] = $price;

        return $this;
    }

    /**
     * Set prices
     *
     * @param \\Paysera\WalletApi\Entity\Location\Price[] $prices
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setPrices(array $prices)
    {
        $this->prices = $prices;

        return $this;
    }

    /**
     * Get prices
     *
     * @return \\Paysera\WalletApi\Entity\Location\Price[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Adds working hours
     *
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function addWorkingHours(DayWorkingHours $workingHours)
    {
        $this->workingHours[] = $workingHours;

        return $this;
    }

    /**
     * Set workingHours
     *
     * @param \\Paysera\WalletApi\Entity\Location\DayWorkingHours[] $workingHours
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setWorkingHours(array $workingHours)
    {
        $this->workingHours = $workingHours;

        return $this;
    }

    /**
     * Get workingHours
     *
     * @return \\Paysera\WalletApi\Entity\Location\DayWorkingHours[]
     */
    public function getWorkingHours()
    {
        return $this->workingHours;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     *
     * @return \Paysera\WalletApi\Entity\Location
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $imagePinClosed
     *
     * @return $this
     */
    public function setImagePinClosed($imagePinClosed)
    {
        $this->imagePinClosed = $imagePinClosed;

        return $this;
    }

    /**
     * @return string
     */
    public function getImagePinClosed()
    {
        return $this->imagePinClosed;
    }

    /**
     * @param string $imagePinOpen
     *
     * @return $this
     */
    public function setImagePinOpen($imagePinOpen)
    {
        $this->imagePinOpen = $imagePinOpen;

        return $this;
    }

    /**
     * @return string
     */
    public function getImagePinOpen()
    {
        return $this->imagePinOpen;
    }

    /**
     * @param array $payCategories
     *
     * @return $this
     */
    public function setPayCategories($payCategories)
    {
        $this->payCategories = $payCategories;

        return $this;
    }

    /**
     * @return array
     */
    public function getPayCategories()
    {
        return $this->payCategories;
    }

    /**
     * @return array
     */
    public function getCashInTypes()
    {
        return $this->cashInTypes;
    }

    /**
     * @param array $cashInTypes
     *
     * @return $this
     */
    public function setCashInTypes($cashInTypes)
    {
        $this->cashInTypes = $cashInTypes;

        return $this;
    }

    /**
     * @return array
     */
    public function getCashOutTypes()
    {
        return $this->cashOutTypes;
    }

    /**
     * @param array $cashOutTypes
     *
     * @return $this
     */
    public function setCashOutTypes($cashOutTypes)
    {
        $this->cashOutTypes = $cashOutTypes;

        return $this;
    }

    /**
     * @param array $services
     *
     * @return $this
     */
    public function setServices($services)
    {
        $this->services = $services;

        return $this;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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

    /**
     * Available location service types
     *
     * @return array
     */
    public static function getServiceTypes()
    {
        return self::$serviceTypes;
    }
}
