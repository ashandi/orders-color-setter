<?php

namespace App\Models;

class Order
{
    /**
     * @var string
     */
    public $number;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $firstname;

    /**
     * @var string
     */
    public $lastname;

    /**
     * @var string
     */
    public $patronymic;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $postcode;

    /**
     * @var int
     */
    public $regionId;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $house;

    /**
     * @var bool
     */
    public $isPrivateHouse;

    /**
     * @var string
     */
    public $apartment;

    /**
     * @var int
     */
    public $departureType;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var string
     */
    public $paymentType;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    private $color;

    /**
     * Returns true if current order has
     * departure type 1
     *
     * @return bool
     */
    public function isPosteRestante()
    {
        return $this->departureType == 1;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * Returns true if current order's payment type
     * isn't "Наложенный платеж"
     *
     * @return bool
     */
    public function isPrepayment()
    {
        return $this->paymentType != 'nal_plat';
    }
}
