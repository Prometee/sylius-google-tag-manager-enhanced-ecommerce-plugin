<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Unit\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener\CheckoutStepListener;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Resolver\CheckoutStepResolverInterface;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\CheckoutStepInterface;
use Sylius\Bundle\CoreBundle\Controller\OrderController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CheckoutStepListenerTest extends TestCase
{
    private MockObject&CheckoutStepInterface $checkoutStep;

    private MockObject&CartContextInterface $cartContext;

    private MockObject&CheckoutStepResolverInterface $checkoutStepResolver;

    private CheckoutStepListener $checkoutStepListener;

    protected function setUp(): void
    {
        $this->checkoutStep = $this->createMock(CheckoutStepInterface::class);
        $this->cartContext = $this->createMock(CartContextInterface::class);
        $this->checkoutStepResolver = $this->createMock(CheckoutStepResolverInterface::class);

        $this->checkoutStepListener = new CheckoutStepListener(
            $this->checkoutStep,
            $this->cartContext,
            $this->checkoutStepResolver,
            0,
        );
    }

    public function testOnKernelControllerWhenEverythingMatches(): void
    {
        $controller = [$this->createMock(OrderController::class), 'summaryAction'];
        $request = new Request();
        $order = $this->createMock(OrderInterface::class);

        $this->checkoutStepResolver->expects($this->once())
            ->method('resolve')
            ->with('summaryAction', $request)
            ->willReturn(0);

        $this->cartContext->expects($this->once())
            ->method('getCart')
            ->willReturn($order);

        $this->checkoutStep->expects($this->once())
            ->method('addStep')
            ->with($order, 0);

        $event = $this->createControllerEvent($controller, $request, true);

        $this->checkoutStepListener->onKernelController($event);
    }

    public function testOnKernelControllerWhenNotMainRequest(): void
    {
        $controller = [$this->createMock(OrderController::class), 'summaryAction'];
        $request = new Request();

        $this->checkoutStepResolver->expects($this->never())->method('resolve');
        $this->cartContext->expects($this->never())->method('getCart');
        $this->checkoutStep->expects($this->never())->method('addStep');

        $event = $this->createControllerEvent($controller, $request, false);

        $this->checkoutStepListener->onKernelController($event);
    }

    public function testOnKernelControllerWhenControllerIsNotArray(): void
    {
        $controller = function () {};
        $request = new Request();

        $this->checkoutStepResolver->expects($this->never())->method('resolve');
        $this->cartContext->expects($this->never())->method('getCart');
        $this->checkoutStep->expects($this->never())->method('addStep');

        $event = $this->createControllerEvent($controller, $request, true);

        $this->checkoutStepListener->onKernelController($event);
    }

    public function testOnKernelControllerWhenControllerNotOrderController(): void
    {
        $controller = [$this->createMock(RedirectController::class), 'redirectAction'];
        $request = new Request();

        $this->checkoutStepResolver->expects($this->never())->method('resolve');
        $this->cartContext->expects($this->never())->method('getCart');
        $this->checkoutStep->expects($this->never())->method('addStep');

        $event = $this->createControllerEvent($controller, $request, true);

        $this->checkoutStepListener->onKernelController($event);
    }

    public function testOnKernelControllerWhenStepIsNull(): void
    {
        $controller = [$this->createMock(OrderController::class), 'summaryAction'];
        $request = new Request();

        $this->checkoutStepResolver->expects($this->once())
            ->method('resolve')
            ->willReturn(null);

        $this->cartContext->expects($this->never())->method('getCart');
        $this->checkoutStep->expects($this->never())->method('addStep');

        $event = $this->createControllerEvent($controller, $request, true);

        $this->checkoutStepListener->onKernelController($event);
    }

    public function testOnKernelControllerWhenStepDoesNotMatch(): void
    {
        $controller = [$this->createMock(OrderController::class), 'summaryAction'];
        $request = new Request();

        $this->checkoutStepResolver->expects($this->once())
            ->method('resolve')
            ->willReturn(1); // Different step than configured (0)

        $this->cartContext->expects($this->never())->method('getCart');
        $this->checkoutStep->expects($this->never())->method('addStep');

        $event = $this->createControllerEvent($controller, $request, true);

        $this->checkoutStepListener->onKernelController($event);
    }

    private function createControllerEvent(callable $controller, Request $request, bool $isMainRequest): ControllerEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $type = $isMainRequest ? HttpKernelInterface::MAIN_REQUEST : HttpKernelInterface::SUB_REQUEST;

        return new ControllerEvent($kernel, $controller, $request, $type);
    }
}
