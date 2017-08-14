<?php

namespace GtmEnhancedEcommercePlugin\EventListener;

use GtmEnhancedEcommercePlugin\TagManager\AddTransactionInterface;
use GtmEnhancedEcommercePlugin\TagManager\CurrencyCodeInterface;
use Sylius\Bundle\CoreBundle\Controller\OrderController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class CurrencyCodeListener
 * @package GtmEnhancedEcommerce\EventListener
 */
class CurrencyCodeListener
{

    /**
     * @var CurrencyCodeInterface
     */
    private $currencyCodeService;


    /**
     * CurrencyCodeListener constructor.
     * @param CurrencyCodeInterface $currencyCode
     */
    public function __construct(
        CurrencyCodeInterface $currencyCode
    ) {
        $this->currencyCodeService = $currencyCode;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        // Add E-Commerce data
        $this->currencyCodeService->addCurrencyCode();
    }
}
