<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Mapper\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

/**
 * Maps grouped products. The listing itself is exported with its minimal
 * price; associated products are referenced through the OpenAI feed
 * "Related Products" schema (`related_product_id`, `relationship_type`).
 */
class GroupedProductMapper extends AbstractProductTypeMapper
{
    private const RELATIONSHIP_PART_OF_SET = 'part_of_set';

    public function map(ProductInterface $product): array
    {
        $row = $this->buildBaseRow($product);

        $typeInstance = $product instanceof Product ? $product->getTypeInstance() : null;

        if ($typeInstance instanceof Grouped) {
            $associatedSkus = [];

            foreach ($typeInstance->getAssociatedProducts($product) as $associatedProduct) {
                if ((int) $associatedProduct->getStatus() !== Status::STATUS_ENABLED) {
                    continue;
                }

                $associatedSkus[] = (string) $associatedProduct->getSku();
            }

            if (!empty($associatedSkus)) {
                $row['related_product_id'] = implode(',', $associatedSkus);
                $row['relationship_type'] = self::RELATIONSHIP_PART_OF_SET;
            }
        }

        return [$row];
    }
}
