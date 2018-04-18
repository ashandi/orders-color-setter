<?php

namespace App\Services;

use App\Models\Order;
use App\Services\ColorHandlers\OrderColorSetter;
use App\Services\ColorHandlers\TestOrderColorSetter;

class OrderColorManager
{
    /**
     * Method checks previous orders of user who made given order
     * and sets specific color for this order if it necessary
     *
     * @param Order $order
     * @param $similarOrders
     */
    public function setColor(Order $order, $similarOrders)
    {
        $orderColorSetter = $this->getFirstElementOfChain();

        $orderColorSetter->setColor($order, $similarOrders);
    }

    /**
     * Returns first node of chain which will set color for order
     *
     * @return OrderColorSetter
     */
    private function getFirstElementOfChain()
    {
        return new TestOrderColorSetter();
    }
}
