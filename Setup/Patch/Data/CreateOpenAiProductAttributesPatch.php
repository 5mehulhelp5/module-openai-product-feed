<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <i.gryshkun@gmail.com>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\ValidateException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Angeo\OpenAiProductFeed\Api\Data\OpenAiProductAttributesInterface;

class CreateOpenAiProductAttributesPatch implements DataPatchInterface, PatchRevertableInterface
{
    private const OPENAI_GROUP_ID = 'OpenAI Configurations';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory
    ) {}

    /**
     * @throws LocalizedException|ValidateException
     */
    public function apply(): self
    {
        $setup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attributeSetIds = $setup->getAllAttributeSetIds(Product::ENTITY);

        foreach ($attributeSetIds as $attributeSetId) {
            $setup->addAttributeGroup(
                Product::ENTITY,
                $attributeSetId,
                self::OPENAI_GROUP_ID,
                100
            );
        }

        $attributesToCreate = $this->getOpenAiAttributesToCreate();

        foreach ($attributesToCreate as $attributeCode => $attributeData) {
            $setup->addAttribute(Product::ENTITY, $attributeCode, $attributeData);

            foreach ($attributeSetIds as $attributeSetId) {
                $setup->addAttributeToGroup(
                    Product::ENTITY,
                    $attributeSetId,
                    self::OPENAI_GROUP_ID,
                    $attributeCode,
                    $attributeData['sort_order']
                );
            }
        }

        return $this;
    }

    public function revert(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->removeAttribute(
            Product::ENTITY,
            OpenAiProductAttributesInterface::OPENAI_ENABLE_SEARCH_ATTRIBUTE
        );
        $eavSetup->removeAttribute(
            Product::ENTITY,
            OpenAiProductAttributesInterface::OPENAI_ENABLE_CHECKOUT_ATTRIBUTE
        );
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    private function getOpenAiAttributesToCreate(): array
    {
        return [
            OpenAiProductAttributesInterface::OPENAI_ENABLE_SEARCH_ATTRIBUTE => [
                'type' => 'int',
                'label' => __('Enable Search'),
                'input' => 'boolean',
                'source' => Boolean::class,
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => self::OPENAI_GROUP_ID,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
            ],

            OpenAiProductAttributesInterface::OPENAI_ENABLE_CHECKOUT_ATTRIBUTE => [
                'type' => 'int',
                'label' => __('Enable Checkout'),
                'input' => 'boolean',
                'source' => Boolean::class,
                'required' => false,
                'sort_order' => 20,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => self::OPENAI_GROUP_ID,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
            ],
        ];
    }
}
