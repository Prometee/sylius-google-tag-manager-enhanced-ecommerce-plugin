<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\OrderInterface;

interface CheckoutStepInterface
{
    public function addStep(OrderInterface $order, string $state): void;
}
