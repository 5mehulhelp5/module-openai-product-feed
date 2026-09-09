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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Angeo\OpenAiProductFeed\Service\GenerateOpenAiFeedService;

class OpenAiProductFeedGeneratorCommand extends Command
{
    public const COMMAND_NAME = 'angeo:product-feed:generate';
    public const COMMAND_DESCRIPTION = 'Execute feed generation for all store views.';
    public const OPTION_STORE = 'store';

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
        $this->addOption(
            self::OPTION_STORE,
            's',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Limit generation to the given store code(s). May be used multiple times. Default: all store views.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting OpenAi Product Feed generation');

        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        } catch (LocalizedException) {
            // Area code is already set: safe to continue.
        }

        $startedAt = microtime(true);
        $storeCodes = array_map('strval', (array) $input->getOption(self::OPTION_STORE));

        $progress = static function (string $storeCode, int $rows, int $total) use ($output): void {
            $output->writeln(sprintf('  <info>%s</info>: %d rows mapped (%d products in scope)', $storeCode, $rows, $total));
        };

        $this->generateFeedService->execute($storeCodes, $progress);

        $output->writeln(sprintf(
            'OpenAi Product feed job finished in %.1f s. Check var/log/angeo_openai_feed.log for any skipped products.',
            microtime(true) - $startedAt
        ));

        return Cli::RETURN_SUCCESS;
    }
}
