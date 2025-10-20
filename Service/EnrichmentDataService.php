<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Service;

use Angeo\OpenAiProductFeed\Api\EnrichmentDataProviderInterface;

class EnrichmentDataService
{
    public function __construct(
        private readonly array $providers
    ) {}

    public function execute(): array
    {
        $result = [];

        foreach ($this->providers as $provider) {
            $data = $provider->provide();

            if (empty($data)) {
                continue;
            }

            $result[] = $data;
        }

        return $result;
    }
}
