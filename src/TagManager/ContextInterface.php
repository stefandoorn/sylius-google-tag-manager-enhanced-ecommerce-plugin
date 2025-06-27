<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\TagManager;

interface ContextInterface
{
    public const CONTEXT_ORDER = 'order';

    public const CONTEXT_ORDER_ITEM = 'order_item';

    public const CONTEXT_PRODUCT = 'product';

    public const CONTEXT_PRODUCTS = 'products';

    public const CONTEXT_CURRENCY_CODE = 'currency_code';

    public const CONTEXT_CHANNEL = 'channel';

    public const CONTEXT_TAXON = 'taxon';
}
