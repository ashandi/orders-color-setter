<?php

namespace App\Services\ColorHandlers;

use App\Models\Order;

/**
 * Class PrepaymentOrderColorSetter
 *
 * PATTERN - Chain of Responsibility
 *
 * @package App\Services\ColorHandlers
 */
class PrepaymentOrderColorSetter extends OrderColorSetter
{

    /**
     * PrepaymentOrderColorSetter constructor.
     */
    public function __construct()
    {
        $this->next = new StolenOrderColorSetter();
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
        if ($order->isPrepayment()) {
            //We don't need to set colors for prepayment orders because we already got money from customer for it
            return;
        } else {
            parent::setColor($order, $similarOrders);
        }
    }
}
