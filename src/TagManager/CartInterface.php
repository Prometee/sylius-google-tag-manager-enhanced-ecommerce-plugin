<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderItemInterface;

interface CartInterface
{
    public function add(OrderItemInterface $orderItem): void;

    public function remove(OrderItemInterface $orderItem): void;
}
