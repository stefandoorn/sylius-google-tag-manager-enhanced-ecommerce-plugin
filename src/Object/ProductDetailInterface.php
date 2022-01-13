<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

interface ProductDetailInterface
{
    public const ID_IDENTIFIER = 'id';

    public const CODE_IDENTIFIER = 'code';

    public const IDENTIFIERS = [self::ID_IDENTIFIER, self::CODE_IDENTIFIER];

    public function getName(): string;

    public function setName(string $name): void;

    public function getId(): string;

    public function setId(string $id): void;

    public function getPrice(): float;

    public function setPrice(float $price): void;

    public function getCategory(): string;

    public function setCategory(string $category);

    public function getVariant(): string;

    public function setVariant(string $variant);

    public function toArray(): array;
}
