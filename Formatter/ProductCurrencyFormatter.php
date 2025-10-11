<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <i.gryshkun@gmail.com>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Formatter;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
class ProductCurrencyFormatter
{
    public function __construct(
        private readonly Data $priceHelper,
        private readonly StoreManagerInterface $storeManager
    ) {}

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function format(float $amount, int $storeId): string
    {
        $price = $this->priceHelper->currencyByStore($amount, $storeId, true, false);
        $currency = $this->storeManager->getStore($storeId)->getCurrentCurrency();

        return str_replace($currency->getCurrencySymbol(), '', $price) . ' ' . $currency->getCode();
    }
}
