<?php

namespace Phpactor\LanguageServer\Tests\Unit\Extension\Core\Handler;

use PHPUnit\Framework\TestCase;
use Phpactor\LanguageServer\Adapter\DTL\DTLArgumentResolver;
use Phpactor\LanguageServer\Core\Dispatcher\MethodDispatcher;
use Phpactor\LanguageServer\Core\Handler;
use Phpactor\LanguageServer\Core\Handlers;
use Phpactor\LanguageServer\Core\Session\Manager;
use Phpactor\LanguageServer\Core\Transport\RequestMessage;

abstract class HandlerTestCase extends TestCase
{
    const EXAMPLE_PROCESS_ID = 1;

    /**
     * @var Manager
     */
    private $manager;

    abstract public function handler(): Handler;

    protected function session(): Manager
    {
        if ($this->manager) {
            return $this->manager;
        }
        $manager = new Manager();
        $manager->initialize(__DIR__);
        $this->manager = $manager;

        return $manager;
    }

    public function dispatch(string $method, array $params): array
    {
        $dispatcher = new MethodDispatcher(
            new DTLArgumentResolver(),
            new Handlers([
                $this->handler()
            ])
        );
        $request = new RequestMessage(self::EXAMPLE_PROCESS_ID, $method, $params);

        $messages = [];

        foreach ($dispatcher->dispatch($request) as $message) {
            $messages[] = $message;
        }

        return $messages;
    }
}