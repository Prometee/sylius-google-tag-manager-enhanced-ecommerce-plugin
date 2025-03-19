<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItemListInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\BreadcrumbComponent;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Grid\View\GridViewInterface;

final class ViewItemListListener
{
    public function __construct(
        private ViewItemListInterface $viewItemList,
        private BreadcrumbComponent $breadcrumbComponent,
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        $taxon = $this->breadcrumbComponent->taxon();
        /** @var GridViewInterface $gridView */
        $gridView = $event->getSubject();

        // Ensure PagerFanta or any other paginator will be handled correctly
        $products = [];
        /** @var iterable<ProductInterface> $data */
        $data = $gridView->getData();
        foreach ($data as $product) {
            $products[] = $product;
        }

        $this->viewItemList->add($taxon, $products);
    }
}
