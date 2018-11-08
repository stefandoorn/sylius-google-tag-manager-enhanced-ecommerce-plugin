<?php declare(strict_types=1);

namespace StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object;

/**
 * Class ProductDetail
 * @package StefanDoorn\SyliusGtmEnhancedEcommercePlugin\Object
 */
final class ProductDetail implements ProductDetailInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string|null
     */
    private $category;

    /**
     * @var string|null
     */
    private $variant;

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @inheritdoc
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @inheritdoc
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @inheritdoc
     */
    public function setCategory(?string $category)
    {
        $this->category = $category;
    }

    /**
     * @inheritdoc
     */
    public function getVariant(): ?string
    {
        return $this->variant;
    }

    /**
     * @inheritdoc
     */
    public function setVariant(?string $variant)
    {
        $this->variant = $variant;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'price' => $this->price,
            'category' => $this->category ?? '',
            'variant' => $this->variant ?? '',
        ];
    }
}
