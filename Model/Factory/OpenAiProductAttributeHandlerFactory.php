<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Model\Factory;

use Magento\Framework\ObjectManagerInterface;
use Angeo\OpenAiProductFeed\Data\OpenAiProductAttributeData;
use Angeo\OpenAiProductFeed\Provider\Product\Data\AttributeHandlers\ProductAttributeProviderInterface;
use Magento\Framework\Exception\LocalizedException;

class OpenAiProductAttributeHandlerFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {}

    /**
     * @throws LocalizedException
     */
    public function create(OpenAiProductAttributeData $attribute): ProductAttributeProviderInterface
    {
        if ($attribute->getProductAttributeHandler() === null) {
            throw new LocalizedException(__('Attribute Handler should be specified.'));
        }

        $handlerClass = $attribute->getProductAttributeHandler();
        $instance = $this->objectManager->create($handlerClass);

        if (!$instance instanceof ProductAttributeProviderInterface) {
            throw new LocalizedException(__('Class ProductAttributeProviderInterface should be implemented.'));
        }

        return $instance;
    }
}
