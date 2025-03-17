<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\GtmProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class ViewItemList implements ViewItemListInterface
{
    /**
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     */
    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private ProductRepositoryInterface $productRepository,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private CurrencyContextInterface $currencyContext,
        private GtmProviderInterface $viewItemListProvider,
    ) {
    }

    public function add(TaxonInterface $taxon): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        /** @var ProductInterface[] $products */
        $products = $this->productRepository->createShopListQueryBuilder(
            $channel,
            $taxon,
            $this->localeContext->getLocaleCode(),
        )->getQuery()->getResult();

        if (0 === count($products)) {
            return;
        }

        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#view_item_details
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $context = [
            ContextInterface::CONTEXT_TAXON => $taxon,
            ContextInterface::CONTEXT_PRODUCTS => $products,
            ContextInterface::CONTEXT_CURRENCY_CODE => $this->currencyContext->getCurrencyCode(),
            ContextInterface::CONTEXT_CHANNEL => $channel,
        ];

        $this->googleTagManager->addPush([
            'event' => $this->viewItemListProvider->getEvent($context),
            'ecommerce' => $this->viewItemListProvider->getEcommerce($context),
        ]);
    }
}
