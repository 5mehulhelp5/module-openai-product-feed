<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <i.gryshkun@gmail.com>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Model\Config\Seller;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SellerConfig
{
    private const SELLER_NAME_CONFIG = 'openai_feed/seller/name';
    private const SELLER_URL_CONFIG = 'openai_feed/seller/url';
    private const SELLER_PRIVACY_POLICY_CONFIG = 'openai_feed/seller/privacy_policy';
    private const SELLER_TOS_CONFIG = 'openai_feed/seller/tos';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    public function getSellerName(): string
    {
        return (string) $this->scopeConfig->getValue(self::SELLER_NAME_CONFIG, ScopeInterface::SCOPE_STORE);
    }

    public function getSellerUrl(): string
    {
        return (string) $this->scopeConfig->getValue(self::SELLER_URL_CONFIG, ScopeInterface::SCOPE_STORE);
    }

    public function getSellerPrivacyPolicy(): string
    {
        return (string) $this->scopeConfig->getValue(self::SELLER_PRIVACY_POLICY_CONFIG, ScopeInterface::SCOPE_STORE);
    }

    public function getSellerTos(): string
    {
        return (string) $this->scopeConfig->getValue(self::SELLER_TOS_CONFIG, ScopeInterface::SCOPE_STORE);
    }
}
