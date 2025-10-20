<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Product;

use Angeo\OpenAiProductFeed\Data\OpenAiProductAttributeData;
use Angeo\OpenAiProductFeed\Provider\Data\AttributeHandlers\ProductAttributeProviderInterface;
use Angeo\OpenAiProductFeed\Model\Factory\OpenAiProductAttributeHandlerFactory;

class AttributeHandlerProvider
{
    private array $handlersPool = [];

    public function __construct(
        private readonly OpenAiProductAttributeHandlerFactory $factory
    ) {}

    public function provide(OpenAiProductAttributeData $attribute): ProductAttributeProviderInterface
    {
        $name = $attribute->getFieldName();

        if (isset($this->handlersPool[$name])) {
            return $this->handlersPool[$name];
        }

        $handler = $this->factory->create($attribute);
        $this->handlersPool[$name] = $handler;

        return $handler;
    }
}
