<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderColorManager;
use App\Repositories\OrderRepository;

class SetOrderColor
{
    /**
     * @var Order
     */
    private $order;

    /**
     * SetOrderColor constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Handle the job
     *
     * @param OrderRepository $orderRepository
     * @param OrderColorManager $colorManager
     */
    public function handle(OrderRepository $orderRepository, OrderColorManager $colorManager)
    {
        $similarOrders = $orderRepository->getSimilarOrders($this->order);

        $colorManager->setColor($this->order, $similarOrders);
    }
}
