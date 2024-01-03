<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\EventListener;

use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Helper\MainRequest\ControllerEventMainRequest;
use StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager\ViewItemListInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

final class ViewItemListListener
{
    private TaxonRepositoryInterface $taxonRepository;

    private LocaleContextInterface $localeContext;

    private FirewallMap $firewallMap;

    private ViewItemListInterface $viewItemList;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        LocaleContextInterface $localeContext,
        FirewallMap $firewallMap,
        ViewItemListInterface $viewItemList
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->localeContext = $localeContext;
        $this->firewallMap = $firewallMap;
        $this->viewItemList = $viewItemList;
    }

    public function __invoke(ControllerEvent $event): void
    {
        if (!ControllerEventMainRequest::isMainRequest($event)) {
            return;
        }

        $request = $event->getRequest();

        $firewallConfig = $this->firewallMap->getFirewallConfig($request);
        if (null !== $firewallConfig && 'shop' !== $firewallConfig->getName()) {
            return;
        }

        if ('sylius_shop_product_index' !== $request->get('_route')) {
            return;
        }

        $slug = $request->get('slug', null);

        if (null === $slug) {
            return;
        }

        $taxon = $this->taxonRepository->findOneBySlug($slug, $this->localeContext->getLocaleCode());

        if (!$taxon instanceof TaxonInterface) {
            return;
        }

        $this->viewItemList->add($taxon, $request->get('_route'));
    }
}
