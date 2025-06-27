<?php

declare(strict_types=1);

namespace Tests\StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Sylius\Behat\Service\Helper\JavaScriptTestHelperInterface;
use Webmozart\Assert\Assert;

final readonly class GtmContext implements Context
{

    public function __construct(
        private Session $session,
        private JavaScriptTestHelperInterface $testHelper,
    ) {
    }

    /**
     * @Then /^the "([^"]*)" Google Tag Manager event should be triggered$/
     */
    public function theGoogleTagManagerEventShouldBeTriggered(string $event): void
    {
        $this->testHelper->waitUntilAssertionPasses(function () use ($event): void {
            $layer = $this->getEventLayer($event);

            Assert::notNull($layer, sprintf(
                'Unable to find the event "%s" layer in the GTM dataLayer.',
                $event,
            ));
        });
    }

    /**
     * @Given /^this event should contain proper "([^"]*)" GTM data$/
     */
    public function thisEventShouldContainProperGtmData(string $event): void
    {
        $this->testHelper->waitUntilAssertionPasses(function () use ($event): void {
            $layer = $this->getEventLayer($event);
            Assert::notNull(
                $layer,
                sprintf(
                    'Unable to find the event "%s" layer in the GTM dataLayer.',
                    $event,
                )
            );

            match ($event) {
                'view_item_list' => $this->checkViewItemList($layer),
                'view_item',
                'view_cart',
                'add_to_cart',
                'remove_from_cart',
                'begin_checkout',
                'add_shipping_info',
                'add_payment_info' => $this->checkViewItem($layer),
                'purchase' => $this->checkPurchase($layer),
                default => throw new \InvalidArgumentException(sprintf('Event "%s" not supported', $event)),
            };
        });
    }

    /**
     * @Given /^this event should not contain proper "([^"]*)" GTM data$/
     */
    public function thisEventShouldNotContainProperGtmData(string $event): void
    {
        $this->testHelper->waitUntilAssertionPasses(function () use ($event): void {
            $layer = $this->getEventLayer($event);
            Assert::null(
                $layer,
                sprintf(
                    'Found the event "%s" layer in the GTM dataLayer, but it should not be there.',
                    var_export($event, true),
                )
            );
        });
    }

    /**
     * @return array<array-key, array<string, mixed>>
     */
    private function getDataLayer(): array
    {
        /** @var array<array-key, array<string, mixed>>|string $dataLayer */
        $dataLayer = $this->session->evaluateScript('return window.dataLayer;');
        Assert::isArray($dataLayer, sprintf('The dataLayer is not an array, found: %s', var_export($dataLayer, true)));

        return $dataLayer;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getEventLayer(string $event): ?array
    {
        foreach ($this->getDataLayer() as $layer) {
            if (($layer['event'] ?? null) === $event) {
                return $layer;
            }
        }

        return null;
    }

    /**
     * @param array<string, array<string, mixed>> $layer
     * @return array<string, mixed>
     */
    private function checkEcommerce(array $layer): array
    {
        Assert::keyExists($layer, 'ecommerce');
        $ecommerce = $layer['ecommerce'];

        Assert::keyExists($ecommerce, 'currency');
        Assert::keyExists($ecommerce, 'items');

        foreach ($ecommerce['items'] as $item) {
            $this->checkItem($item);
        }

        return $ecommerce;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function checkItem(array $item): void
    {
        Assert::keyExists($item, 'item_id');
        Assert::keyExists($item, 'item_name');
    }

    private function checkViewItemList(array $layer): void
    {
        $ecommerce = $this->checkEcommerce($layer);

        if (isset($ecommerce['item_list_id'])) {
            Assert::notNull($ecommerce['item_list_id']);
            Assert::notEmpty($ecommerce['item_list_id']);
        }

        if (isset($ecommerce['item_list_name'])) {
            Assert::notNull($ecommerce['item_list_name']);
            Assert::notEmpty($ecommerce['item_list_name']);
        }
    }

    private function checkViewItem(array $layer): void
    {
        $ecommerce = $this->checkEcommerce($layer);

        Assert::keyExists($ecommerce, 'value');
    }

    private function checkPurchase(array $layer): void
    {
        $ecommerce = $this->checkEcommerce($layer);

        Assert::keyExists($ecommerce, 'value');
        Assert::keyExists($ecommerce, 'transaction_id');
    }
}
