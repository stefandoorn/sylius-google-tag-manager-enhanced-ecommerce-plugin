<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

/**
 * Interface ProductDetailInterface
 * @package StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object
 */
interface ProductDetailInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     */
    public function setId(int $id): void;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @param float $price
     */
    public function setPrice(float $price): void;

    /**
     * @return null|string
     */
    public function getCategory(): ?string;

    /**
     * @param null|string $category
     */
    public function setCategory(?string $category);

    /**
     * @return null|string
     */
    public function getVariant(): ?string;

    /**
     * @param null|string $variant
     */
    public function setVariant(?string $variant);

    /**
     * @return array
     */
    public function toArray(): array;
}
