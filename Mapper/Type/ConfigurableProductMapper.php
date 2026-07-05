<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Mapper\Type;

use Angeo\OpenAiProductFeed\Provider\Product\ProductAttributeHandlerProvider;
use Angeo\OpenAiProductFeed\Provider\Product\ProductAttributesDataProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * Maps configurable products to a parent listing row plus one row per
 * enabled child variant, following the OpenAI feed "Variants" schema:
 * `group_id`, `listing_has_variations`, `variant_dict`, `item_group_title`.
 */
class ConfigurableProductMapper extends AbstractProductTypeMapper
{
    public function __construct(
        ProductAttributeHandlerProvider $handlerProvider,
        ProductAttributesDataProvider $attributesDataProvider,
        LoggerInterface $logger,
        private readonly ProductResource $productResource,
        private readonly SerializerInterface $serializer
    ) {
        parent::__construct($handlerProvider, $attributesDataProvider, $logger);
    }

    public function map(ProductInterface $product): array
    {
        $groupId = (string) $product->getSku();
        $groupTitle = (string) $product->getName();

        $parentRow = $this->buildBaseRow($product);
        $parentRow['group_id'] = $groupId;
        $parentRow['listing_has_variations'] = 'true';
        $parentRow['item_group_title'] = $groupTitle;

        $rows = [$parentRow];

        $typeInstance = $product->getTypeInstance();

        if (!$typeInstance instanceof Configurable || !$product instanceof Product) {
            return $rows;
        }

        $variantAttributes = $this->getVariantAttributes($typeInstance, $product);

        foreach ($typeInstance->getUsedProducts($product) as $child) {
            if ((int) $child->getStatus() !== Status::STATUS_ENABLED) {
                continue;
            }

            try {
                $childRow = $this->buildBaseRow($child);
            } catch (\Throwable $exception) {
                $this->logger->warning(
                    sprintf(
                        '[Angeo_OpenAiProductFeed] Skipped variant SKU "%s" of "%s": %s',
                        (string) $child->getSku(),
                        $groupId,
                        $exception->getMessage()
                    ),
                    ['exception' => $exception]
                );
                continue;
            }

            $childRow['group_id'] = $groupId;
            $childRow['listing_has_variations'] = 'true';
            $childRow['item_group_title'] = $groupTitle;
            $childRow['variant_dict'] = $this->buildVariantDict($variantAttributes, $child);

            // Children are typically not visible individually: reuse parent
            // URL and image when the child does not provide its own.
            $childRow['url'] = $parentRow['url'] ?? '';

            if (($childRow['image_url'] ?? '') === '') {
                $childRow['image_url'] = $parentRow['image_url'] ?? '';
            }

            $rows[] = $childRow;
        }

        return $rows;
    }

    /**
     * @return array<string, string> attribute code => store label
     */
    private function getVariantAttributes(Configurable $typeInstance, Product $product): array
    {
        $attributes = [];

        foreach ($typeInstance->getConfigurableAttributes($product) as $configurableAttribute) {
            $productAttribute = $configurableAttribute->getProductAttribute();

            if ($productAttribute === null) {
                continue;
            }

            $code = (string) $productAttribute->getAttributeCode();
            $label = (string) ($productAttribute->getStoreLabel()
                ?: $productAttribute->getDefaultFrontendLabel()
                ?: $code);

            $attributes[$code] = $label;
        }

        return $attributes;
    }

    /**
     * @param array<string, string> $variantAttributes
     */
    private function buildVariantDict(array $variantAttributes, ProductInterface $child): string
    {
        $variantDict = [];

        foreach ($variantAttributes as $code => $label) {
            $attribute = $this->productResource->getAttribute($code);

            if (!$attribute) {
                continue;
            }

            $optionText = $attribute->getSource()->getOptionText($child->getData($code));

            if (is_array($optionText)) {
                $optionText = current($optionText);
            }

            $optionText = (string) $optionText;

            if ($optionText !== '') {
                $variantDict[$label] = $optionText;
            }
        }

        if (empty($variantDict)) {
            return '';
        }

        return (string) $this->serializer->serialize($variantDict);
    }
}
