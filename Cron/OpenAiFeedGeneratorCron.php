<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Cron;

use Angeo\OpenAiProductFeed\Service\GenerateOpenAiFeedService;

class OpenAiFeedGeneratorCron
{
    public function __construct(
        private readonly GenerateOpenAiFeedService $generateFeedService
    ) {}

    public function execute(): void
    {
        $this->generateFeedService->execute();
    }
}
