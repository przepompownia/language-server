<?php

namespace Phpactor\LanguageServer\Extension\Command;

use Phpactor\LanguageServer\LanguageServerBuilder;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    const NAME = 'language-server';

    /**
     * @var LanguageServerBuilder
     */
    private $languageServerBuilder;

    public function __construct(LanguageServerBuilder $languageServerBuilder)
    {
        parent::__construct();
        $this->languageServerBuilder = $languageServerBuilder;
    }

    protected function configure(): void
    {
        $this->setDescription('Start Language Server');
        $this->addOption('address', null, InputOption::VALUE_REQUIRED, 'Start a TCP server at this address (e.g. 127.0.0.1:0)');
        $this->addOption('record', null, InputOption::VALUE_OPTIONAL, 'Record requests to log');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $builder = $this->languageServerBuilder;

        $this->logMessage($output, '<info>Starting language server, use -vvv for verbose output</>');

        $record = $input->getOption('record');
        if ($record && is_string($record)) {
            $filename = $this->assertIsWritable($record);
            $this->logMessage($output, sprintf('<info>Recording output to:</> %s', $filename));
            $builder->recordTo($filename);
        }

        $address = $input->getOption('address');
        if ($address && is_string($address)) {
            $this->configureTcpServer($address, $builder);
        }

        $server = $builder->build();
        $server->start();

        return 0;
    }

    private function configureTcpServer(string $address, LanguageServerBuilder $builder): void
    {
        assert(is_string($address));
        $builder->tcpServer($address);
    }

    private function assertIsWritable(?string $filename = null): string
    {
        if (null === $filename) {
            $filename = 'language-server-request.log';
        }

        if (!file_exists(dirname($filename))) {
            throw new RuntimeException(sprintf('Directory "%s" does not exist', dirname($filename)));
        }

        if (file_exists($filename) && !is_writable($filename)) {
            throw new RuntimeException(sprintf('File at "%s" is not writable', $filename));
        }

        return $filename;
    }

    private function logMessage(OutputInterface $output, string $message): void
    {
        if ($output instanceof ConsoleOutput) {
            $output->getErrorOutput()->writeln(
                $message
            );
        }
    }
}
