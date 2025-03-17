<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CartInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CartListener
{
    public function __construct(
        private CartInterface $cart,
    ) {
    }

    public function onAddToCart(GenericEvent $event): void
    {
        /** @var AddToCartCommandInterface|mixed $addToCartCommand */
        $addToCartCommand = $event->getSubject();
        Assert::isInstanceOf($addToCartCommand, AddToCartCommandInterface::class);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $addToCartCommand->getCartItem();

        $this->cart->add($orderItem);
    }

    public function onRemoveFromCart(GenericEvent $event): void
    {
        /** @var OrderItemInterface|mixed $orderItem */
        $orderItem = $event->getSubject();
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);

        $this->cart->remove($orderItem);
    }
}
