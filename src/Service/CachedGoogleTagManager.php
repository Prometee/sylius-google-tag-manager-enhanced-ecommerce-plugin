<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Service\ResetInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class CachedGoogleTagManager implements GoogleTagManagerInterface, ResetInterface
{
    public const GTM_DATA_LAYER = 'gtm_data_layer';

    private bool $pushCalled = false;

    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        protected AdapterInterface $cache,
    ) {
        $cacheItem = $this->cache->getItem(self::GTM_DATA_LAYER);
        /** @var array<string, array<string, mixed>> $cachedPushes */
        $cachedPushes = $cacheItem->isHit() ? $cacheItem->get() : [];
        foreach ($cachedPushes as $push) {
            $this->addPush($push);
        }
    }

    public function addPush($value): void
    {
        $this->googleTagManager->addPush($value);

        $cacheItem = $this->cache->getItem(self::GTM_DATA_LAYER);
        $cacheItem->set($this->getPush());
        $this->cache->save($cacheItem);

        $this->pushCalled = false;
    }

    public function getPush(): array
    {
        $this->pushCalled = true;
        return $this->googleTagManager->getPush();
    }

    public function reset(): void
    {
        $this->googleTagManager->reset();
        if (false === $this->pushCalled) {
            return;
        }

        $this->pushCalled = false;

        $this->cache->delete(self::GTM_DATA_LAYER);
    }

    public function addData(string $key, mixed $value): void
    {
        $this->googleTagManager->addData($key, $value);
    }

    public function setData(string $key, mixed $value): void
    {
        $this->googleTagManager->setData($key, $value);
    }

    public function mergeData(string $key, mixed $value): void
    {
        $this->googleTagManager->mergeData($key, $value);
    }

    public function enable(): void
    {
        $this->googleTagManager->enable();
    }

    public function disable(): void
    {
        $this->googleTagManager->disable();
    }

    public function isEnabled(): bool
    {
        return $this->googleTagManager->isEnabled();
    }

    public function getId(): string
    {
        return $this->googleTagManager->getId();
    }

    public function setId(string $id): void
    {
        $this->googleTagManager->setId($id);
    }

    public function getData(): array
    {
        return $this->googleTagManager->getData();
    }

    public function hasData(): bool
    {
        return $this->googleTagManager->hasData();
    }

    public function setAdditionalParameters(string $additionalParameters): void
    {
        $this->googleTagManager->setAdditionalParameters($additionalParameters);
    }

    public function getAdditionalParameters(): string
    {
        return $this->googleTagManager->getAdditionalParameters();
    }
}
