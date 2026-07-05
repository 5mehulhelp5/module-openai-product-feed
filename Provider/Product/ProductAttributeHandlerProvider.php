<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product;

use Angeo\OpenAiProductFeed\Data\OpenAiProductAttributeData;
use Angeo\OpenAiProductFeed\Model\Factory\OpenAiProductAttributeHandlerFactory;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\ProductAttributeProviderInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Caches attribute handler instances so a handler is created once per feed
 * run instead of once per product row.
 */
class ProductAttributeHandlerProvider
{
    /**
     * @var array<string, ProductAttributeProviderInterface>
     */
    private array $handlersPool = [];

    public function __construct(
        private readonly OpenAiProductAttributeHandlerFactory $factory
    ) {}

    /**
     * @throws LocalizedException
     */
    public function provide(OpenAiProductAttributeData $attribute): ProductAttributeProviderInterface
    {
        $handlerClass = (string) $attribute->getProductAttributeHandler();

        if (!isset($this->handlersPool[$handlerClass])) {
            $this->handlersPool[$handlerClass] = $this->factory->create($attribute);
        }

        return $this->handlersPool[$handlerClass];
    }
}
