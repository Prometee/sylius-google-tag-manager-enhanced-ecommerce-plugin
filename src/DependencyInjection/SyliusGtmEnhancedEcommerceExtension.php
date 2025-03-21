<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class SyliusGtmEnhancedEcommerceExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        $loader->load('services.yaml');

        $container->setParameter('sylius_gtm_enhanced_ecommerce.product_identifier', $config['product_identifier']);

        foreach ($config['features'] as $feature => $setting) {
            $parameter = \sprintf('sylius_gtm_enhanced_ecommerce.features.%s', $feature);

            $container->setParameter($parameter, $setting);

            if ($setting === true || (\is_array($setting) && $setting['enabled'] === true)) {
                $loader->load(\sprintf('features/%s.yaml', $feature));
            }
        }
    }
}
