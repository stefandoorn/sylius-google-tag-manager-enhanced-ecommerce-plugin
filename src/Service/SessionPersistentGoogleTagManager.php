<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Service\ResetInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class SessionPersistentGoogleTagManager implements PersistentGoogleTagManagerInterface, ResetInterface
{
    public const GTM_PERSISTED_PUSH = 'gtm_persisted_push';

    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private RequestStack $requestStack,
    ) {
    }

    public function addPush($value): void
    {
        if ($this->persistPush($value)) {
            return;
        }

        $this->googleTagManager->addPush($value);
    }

    public function getPush(): array
    {
        $this->addPersistedPush();
        $this->clearPersistedPush();

        return $this->googleTagManager->getPush();
    }

    private function persistPush(mixed $value): bool
    {
        $session = $this->getRequestSession();
        if (null === $session) {
            return false;
        }

        /** @var mixed[] $push */
        $push = $session->get(self::GTM_PERSISTED_PUSH, []);
        $push[] = $value;

        $session->set(self::GTM_PERSISTED_PUSH, $push);

        return true;
    }

    private function addPersistedPush(): void
    {
        $session = $this->getRequestSession();
        if (null === $session) {
            return;
        }

        /** @var array<string, array<string, mixed>> $persistedPushes */
        $persistedPushes = $session->get(self::GTM_PERSISTED_PUSH, []);

        foreach ($persistedPushes as $push) {
            $this->googleTagManager->addPush($push);
        }
    }

    private function clearPersistedPush(): void
    {
        $session = $this->getRequestSession();
        if (null === $session) {
            return;
        }

        $session->remove(self::GTM_PERSISTED_PUSH);
    }

    private function getRequestSession(): ?SessionInterface
    {
        $mainRequest = $this->requestStack->getMainRequest();
        if (null === $mainRequest) {
            return null;
        }

        if (!$mainRequest->hasSession()) {
            return null;
        }

        return $mainRequest->getSession();
    }

    public function reset(): void
    {
        $this->googleTagManager->reset();
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
