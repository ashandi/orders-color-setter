<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepository
{
    /**
     * Returns orders from the same user who made given order
     *
     * @param Order $order
     * @return mixed
     */
    public function getSimilarOrders(Order $order);
}
