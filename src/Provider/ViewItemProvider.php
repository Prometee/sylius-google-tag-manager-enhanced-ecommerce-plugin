<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ContextInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

class ViewItemProvider extends ViewItemListProvider
{
    public function getEvent(array $context): ?string
    {
        return 'view_item';
    }

    public function getEcommerce(array $context): ?array
    {
        Assert::keyExists($context, ContextInterface::CONTEXT_PRODUCT);

        /** @var ProductInterface|mixed $product */
        $product = $context[ContextInterface::CONTEXT_PRODUCT];
        Assert::isInstanceOf($product, ProductInterface::class);

        $context[ContextInterface::CONTEXT_PRODUCTS] = [$product];

        return parent::getEcommerce($context);
    }
}
