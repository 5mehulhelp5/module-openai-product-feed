<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Formatter;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Formats prices as `<amount> <ISO 4217 code>` (e.g. `79.99 USD`) as required
 * by the OpenAI feed specification. No locale symbols, no thousands separators.
 */
class ProductCurrencyFormatter
{
    public function __construct(
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly StoreManagerInterface $storeManager
    ) {}

    /**
     * @throws NoSuchEntityException
     */
    public function format(float $amount, int $storeId): string
    {
        $store = $this->storeManager->getStore($storeId);
        $converted = $this->priceCurrency->convertAndRound($amount, $storeId);

        return number_format((float) $converted, 2, '.', '')
            . ' '
            . $store->getCurrentCurrency()->getCode();
    }
}
