<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Provider\GtmProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use Xynnn\GoogleTagManagerBundle\Service\GoogleTagManagerInterface;

final class CheckoutStep implements CheckoutStepInterface
{
    /**
     * @param ServiceProviderInterface<GtmProviderInterface> $locator
     */
    public function __construct(
        private GoogleTagManagerInterface $googleTagManager,
        private ServiceProviderInterface $locator,
    ) {
    }

    public function addStep(OrderInterface $order, string $state): void
    {
        // https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtm#initiate_the_checkout_process
        $this->googleTagManager->addPush([
            'ecommerce' => null,
        ]);

        $provider = $this->locator->get($state);

        $context = [
            ContextInterface::CONTEXT_ORDER => $order,
        ];

        $this->googleTagManager->addPush([
            'event' => $provider->getEvent($context),
            'ecommerce' => $provider->getEcommerce($context),
        ]);
    }
}
