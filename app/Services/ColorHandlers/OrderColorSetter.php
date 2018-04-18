<?php

namespace App\Services\ColorHandlers;

/**
 * Class OrderColorSetter
 *
 * PATTERN - Chain of Responsibility
 *
 * @package App\Services\ColorHandlers
 */
abstract class OrderColorSetter
{
    /**
     * @var OrderColorSetter
     */
    protected $next;

    /**
     * Method sets color to given order if it necessary.
     * Else it calls setColor form next Setter
     *
     * @param $order
     * @param $similarOrders
     */
    public function setColor($order, $similarOrders)
    {
        if ($this->next) {
            $this->next->setColor($order, $similarOrders);
        }
    }
}
