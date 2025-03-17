<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener\CartListener;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CartInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CartListenerTest extends TestCase
{
    private MockObject&CartInterface $cartMock;

    private CartListener $cartListener;

    protected function setUp(): void
    {
        $this->cartMock = $this->createMock(CartInterface::class);
        $this->cartListener = new CartListener($this->cartMock);
    }

    public function testAddToCartEventDispatch(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);

        $addToCartCommand = $this->createMock(AddToCartCommandInterface::class);
        $addToCartCommand->expects($this->atLeastOnce())
            ->method('getCartItem')
            ->willReturn($orderItem);

        $this->cartMock->expects($this->once())
            ->method('add')
            ->with($orderItem);

        $event = new GenericEvent($addToCartCommand);
        $this->cartListener->onAddToCart($event);
    }

    public function testRemoveFromCartEventDispatch(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);

        $this->cartMock->expects($this->once())
            ->method('remove')
            ->with($orderItem);

        $event = new GenericEvent($orderItem);
        $this->cartListener->onRemoveFromCart($event);
    }
}
