<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItemListInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\TaxonInterface;

final class ViewItemListListener
{
    public function __construct(
        private ViewItemListInterface $viewItemList,
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        /** @var TaxonInterface $taxon */
        $taxon = $event->getSubject();

        $this->viewItemList->add($taxon);
    }
}
