<?php

/**
 * @copyright Copyright (c) 2025 Ievgenii Gryshkun
 * @author    Ievgenii Gryshkun <info@angeo.dev>
 * @license   MIT
 */

declare(strict_types=1);

namespace Angeo\OpenAiProductFeed\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Angeo\OpenAiProductFeed\Service\GenerateOpenAiFeedService;

class OpenAiProductFeedGeneratorCommand extends Command
{
    public const string COMMAND_NAME = 'angeo:product-feed:generate';
    public const string COMMAND_DESCRIPTION = 'Execute feed generation for all store views.';

    public function __construct(
        private readonly State $state,
        private readonly GenerateOpenAiFeedService $generateFeedService,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);
    }

    /**
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting OpenAi Product Feed generation');

        $this->state->setAreaCode(Area::AREA_ADMINHTML);

        $this->generateFeedService->execute();

        $output->writeln('OpenAi Product feed job was finished successfully.');

        return Cli::RETURN_SUCCESS;
    }
}
