<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Factory\GtmEcommerceFactoryInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\RemoveFromCartProvider;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class RemoveFromCartProviderTest extends TestCase
{
    private MockObject&GtmEcommerceFactoryInterface $gtmEcommerceFactory;

    private RemoveFromCartProvider $provider;

    protected function setUp(): void
    {
        $this->gtmEcommerceFactory = $this->createMock(GtmEcommerceFactoryInterface::class);
        $this->provider = new RemoveFromCartProvider($this->gtmEcommerceFactory);
    }

    public function testEventReturnsCorrectConstant(): void
    {
        self::assertSame('remove_from_cart', $this->provider->getEvent([]));
    }

    public function testGetEcommerceReturnsDataFromFactory(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);
        $expected = [
            'currency' => 'USD',
            'items' => [
                [
                    'id' => 'product123',
                ],
            ],
        ];

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromSingleOrderItem')
            ->with($orderItem)
            ->willReturn($expected);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER_ITEM => $orderItem,
        ]);

        self::assertSame($expected, $result);
    }

    public function testGetEcommerceThrowsExceptionWhenOrderItemKeyMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([]);
    }

    public function testGetEcommerceThrowsExceptionWhenOrderItemIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER_ITEM => new \stdClass(),
        ]);
    }

    public function testGetEcommerceReturnsNullWhenFactoryReturnsNull(): void
    {
        $orderItem = $this->createMock(OrderItemInterface::class);

        $this->gtmEcommerceFactory->expects($this->once())
            ->method('createNewFromSingleOrderItem')
            ->with($orderItem)
            ->willReturn(null);

        $result = $this->provider->getEcommerce([
            ContextInterface::CONTEXT_ORDER_ITEM => $orderItem,
        ]);

        self::assertNull($result);
    }
}
