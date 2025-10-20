<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Model\Config\ReturnPolicy;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ReturnPoliceConfig
{
    private const RETURN_POLICY_URL_CONFIG = 'openai_feed/return_policy/url';
    private const RETURN_WINDOW_CONFIG = 'openai_feed/return_policy/return_window';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    public function getReturnPolicyUrl(): string
    {
        return (string) $this->scopeConfig->getValue(self::RETURN_POLICY_URL_CONFIG, ScopeInterface::SCOPE_STORE);
    }

    public function getReturnWindow(): int
    {
        return (int) $this->scopeConfig->getValue(self::RETURN_WINDOW_CONFIG, ScopeInterface::SCOPE_STORE);
    }
}
