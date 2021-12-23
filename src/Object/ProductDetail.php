<?php

declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

final class ProductDetail implements ProductDetailInterface
{
    private string $name;

    private int $id;

    private float $price;

    private ?string $category;

    private ?string $variant;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category)
    {
        $this->category = $category;
    }

    public function getVariant(): ?string
    {
        return $this->variant;
    }

    public function setVariant(?string $variant)
    {
        $this->variant = $variant;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'price' => $this->price,
            'category' => isset($this->category) && $this->category ?? '',
            'variant' => isset($this->variant) && $this->variant ?? '',
        ];
    }
}
