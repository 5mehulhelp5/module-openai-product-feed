<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Provider\ReturnPolicy;

use Angeo\OpenAiProductFeed\Api\EnrichmentDataProviderInterface;
use Angeo\OpenAiProductFeed\Model\Config\ReturnPolicy\ReturnPoliceConfig;

class ReturnPolicyDataProvider implements EnrichmentDataProviderInterface
{
    public function __construct(
        private readonly ReturnPoliceConfig $returnPolicyConfig
    ) {}

    public function provide(): array
    {
        return [
            'return_policy' => $this->returnPolicyConfig->getReturnPolicyUrl(),
            'return_window' => $this->returnPolicyConfig->getReturnWindow(),
        ];
    }
}
