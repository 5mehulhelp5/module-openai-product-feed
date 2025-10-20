<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\Seller;

use Angeo\OpenAiProductFeed\Api\EnrichmentDataProviderInterface;
use Angeo\OpenAiProductFeed\Model\Config\Seller\SellerConfig;

class SellerInfoDataProvider implements EnrichmentDataProviderInterface
{
    public function __construct(
        private readonly SellerConfig $sellerConfig
    ) {}
    public function provide(): array
    {
        return [
            'seller_name' => $this->sellerConfig->getSellerName(),
            'seller_url' => $this->sellerConfig->getSellerUrl(),
            'seller_privacy_policy' => $this->sellerConfig->getSellerPrivacyPolicy(),
            'seller_tos' => $this->sellerConfig->getSellerTos(),
        ];
    }
}
