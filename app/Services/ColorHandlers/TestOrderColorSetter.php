<?php

namespace App\Services\ColorHandlers;

use App\Models\Order;

/**
 * Class TestOrderColorSetter
 *
 * PATTERN - Chain of Responsibility
 *
 * @package App\Services\ColorHandlers
 */
class TestOrderColorSetter extends OrderColorSetter
{
    /**
     * TestOrderColorSetter constructor.
     */
    public function __construct()
    {
        $this->next = new PrepaymentOrderColorSetter();
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
        if ($this->isTestOrder($order)) {
            $order->setColor('grey');
        } else {
            parent::setColor($order, $similarOrders);
        }
    }

    /**
     * Returns true if given order has been made in test goals
     *
     * @param Order $order
     * @return bool
     */
    private function isTestOrder($order)
    {
        return ! (strpos($order->comment, 'test') === false);
    }
}
