<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

interface ViewItemListInterface
{
    /**
     * @param ProductInterface[] $products
     */
    public function add(TaxonInterface $taxon, array $products): void;
}
