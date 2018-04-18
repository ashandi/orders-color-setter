<?php

namespace App\Services\ColorHandlers;

use App\Models\Order;

/**
 * Class ReturnedOrderColorSetter
 *
 * PATTERN - Chain of Responsibility
 *
 * @package App\Services\Order\ColorHandlers
 */
class ReturnedOrderColorSetter extends OrderColorSetter
{
    /**
     * ReturnedOrderColorSetter constructor.
     */
    public function __construct()
    {
        $this->next = null;
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
        $ordersHistory = $this->prepareOrdersHistory($similarOrders);

        if($this->latestOrderIsBad($ordersHistory)) {
            if ($this->secondOrderIsBad($ordersHistory)) {
                $order->setColor('orange');
                return;
            }

            $order->setColor('yellow');
        } else {
            parent::setColor($order, $similarOrders);
        }
    }

    /**
     * Method removed all canceled orders
     * from given collection
     *
     * @param $similarOrders
     * @return mixed
     */
    private function prepareOrdersHistory($similarOrders)
    {
        return array_filter($similarOrders, function (Order $order) {
                return $order->status != 'canceled';
        });
    }

    /**
     * Method checks last order from history
     *
     * @param $ordersHistory
     * @return bool
     */
    private function latestOrderIsBad($ordersHistory)
    {
        $latestOrder = $ordersHistory->first();

        return $latestOrder
            ? $this->orderIsBad($latestOrder)
            : false;
    }

    /**
     * Method checks order before latest from history
     *
     * @param $ordersHistory
     * @return bool
     */
    private function secondOrderIsBad($ordersHistory)
    {
        $secondOrder = $ordersHistory->slice(1, 1)->first(); //$ordersHistory->second();

        return $secondOrder
            ? $this->orderIsBad($secondOrder)
            : false;
    }

    /**
     * Returns true if given order
     * was returned
     *
     * @param Order $order
     * @return bool
     */
    private function orderIsBad($order)
    {
        return $order->status == 'canceled';
    }
}
