<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

use Sylius\Component\Core\Model\TaxonInterface;

interface ViewItemListInterface
{
    public function add(TaxonInterface $taxon, ?string $listId = null): void;
}
