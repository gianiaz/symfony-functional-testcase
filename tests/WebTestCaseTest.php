<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase\Tests;

use Facile\SymfonyFunctionalTestCase\Tests\App\AppKernel;
use Facile\SymfonyFunctionalTestCase\WebTestCase;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WebTestCaseTest extends WebTestCase
{
    public function setUp(): void
    {
        static::$class = AppKernel::class;
    }

    public static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    public function testGetContainer(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->getContainer());
    }

    /**
     * Call methods from Symfony to ensure the Controller works.
     */
    public function testIndex(): void
    {
        $path = '/';
        $client = static::createClient();

        $crawler = $client->request('GET', $path);

        $this->assertSame(1, $crawler->filter('html > body')->count());

        $this->assertSame(
            'Not logged in.',
            $crawler->filter('p#user')->text()
        );

        $this->assertSame(
            'LiipFunctionalTestBundle',
            $crawler->filter('h1')->text()
        );
    }

    /**
     * @depends testIndex
     */
    public function testIndexAssertStatusCode(): void
    {
        $path = '/';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertStatusCode(200, $client);
    }

    /**
     * @depends testIndex
     */
    public function testIndexAssertIsSuccessful(): void
    {
        $path = '/';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertStatusCodeIsSuccessful($client);
    }

    /**
     * @depends testIndex
     */
    public function testIndexAssertIsRedirect(): void
    {
        $path = '/redirect';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertStatusCodeIsRedirect($client);
    }

    /**
     * @depends testIndex
     */
    public function testAssertStatusCodeFail(): void
    {
        $path = '/';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('-1');

        $this->assertStatusCode(-1, $client);
    }

    public function test404Error(): void
    {
        $path = '/missing_page';
        $client = static::createClient();

        $client->request('GET', $path);

        $this->assertStatusCode(404, $client);
    }
}
