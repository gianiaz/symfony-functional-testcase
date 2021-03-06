<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ResettableContainerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class WebTestCase extends BaseWebTestCase
{
    /** @var string */
    protected $environment = 'test';

    /** @var ContainerInterface[] */
    protected $containers = [];

    /**
     * Builds up the environment to run the given command.
     */
    protected function runCommand(string $name, array $params = [], bool $reuseKernel = false): CommandTester
    {
        if (! $reuseKernel) {
            if (null !== static::$kernel) {
                static::$kernel->shutdown();
            }

            $kernel = static::$kernel = static::createKernel(['environment' => $this->environment]);
            $kernel->boot();
        } else {
            $kernel = $this->getContainer()->get('kernel');
        }

        $application = new Application($kernel);

        $command = $application->find($name);
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(['command' => $command->getName()], $params),
            [
                'interactive' => false,
            ]
        );

        return $commandTester;
    }

    /**
     * Get an instance of the dependency injection container.
     * (this creates a kernel *without* parameters).
     */
    protected function getContainer(): ContainerInterface
    {
        $cacheKey = $this->environment;
        if (empty($this->containers[$cacheKey])) {
            $options = [
                'environment' => $this->environment,
            ];
            $kernel = $this->createKernel($options);
            $kernel->boot();

            $container = $kernel->getContainer();
            if ($container->has('test.service_container')) {
                $this->containers[$cacheKey] = $container->get('test.service_container');
            } else {
                $this->containers[$cacheKey] = $container;
            }
        }

        return $this->containers[$cacheKey];
    }

    /**
     * Asserts that the HTTP response code of the last request performed by
     * $client matches the expected code. If not, raises an error with more
     * information.
     */
    public function assertStatusCode(int $expectedStatusCode, Client $client, string $message = ''): void
    {
        $response = $client->getResponse();

        $this->assertInstanceOf(Response::class, $response, 'Response missing from client');
        $this->assertSame($expectedStatusCode, $response->getStatusCode(), $message);
    }

    protected function assertStatusCodeIsSuccessful(Client $client): void
    {
        $response = $client->getResponse();

        $this->assertInstanceOf(Response::class, $response, 'Response missing from client');
        $this->assertTrue($response->isSuccessful(), 'HTTP status code not successful: ' . $response->getStatusCode());
    }

    protected function assertStatusCodeIsRedirect(Client $client): void
    {
        $response = $client->getResponse();

        $this->assertInstanceOf(Response::class, $response, 'Response missing from client');
        $this->assertTrue($response->isRedirect(), 'HTTP status code not a redirect: ' . $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        foreach ($this->containers as $container) {
            if ($container instanceof ResettableContainerInterface) {
                $container->reset();
            }
        }

        $this->containers = [];

        parent::tearDown();
    }
}
