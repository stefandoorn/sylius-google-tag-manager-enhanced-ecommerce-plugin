<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

interface ProductDetailInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getId(): int;

    public function setId(int $id): void;

    public function getPrice(): float;

    public function setPrice(float $price): void;

    public function getCategory(): ?string;

    public function setCategory(?string $category);

    public function getVariant(): ?string;

    public function setVariant(?string $variant);

    public function toArray(): array;
}
