<?php

namespace App\Services\ColorHandlers;

use App\Models\Order;

/**
 * Class StolenOrderColorSetter
 *
 * PATTERN - Chain of Responsibility
 *
 * @package App\Services\ColorHandlers
 */
class StolenOrderColorSetter extends OrderColorSetter
{

    /**
     * StolenOrderColorSetter constructor.
     */
    public function __construct()
    {
        $this->next = new ReturnedOrderColorSetter();
    }

    /**
     * Method sets color to given order if it necessary.
     * Else it calls setColor form next Setter
     *
     * @param Order $order
     * @param $similarOrders
     */
    public function setColor($order, $similarOrders)
    {
        if ($this->hasStolenOrdersInHistory($similarOrders)) {
            $order->setColor('red');
        } else {
            parent::setColor($order, $similarOrders);
        }
    }

    /**
     * Returns true if given collection of orders has
     * orders in status 'Stolen'
     *
     * @param $similarOrders
     * @return bool
     */
    private function hasStolenOrdersInHistory($similarOrders)
    {
        $stolenOrders = array_filter($similarOrders, function (Order $order) {
            return $order->status == 'stolen';
        });

        return count($stolenOrders) > 0;
    }
}
