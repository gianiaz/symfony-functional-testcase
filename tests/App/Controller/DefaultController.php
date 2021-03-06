<?php

declare(strict_types=1);

namespace Facile\SymfonyFunctionalTestCase\Tests\App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function indexAction(): Response
    {
        return new Response('Hello world');
    }

    public function redirectAction(): RedirectResponse
    {
        return new RedirectResponse('/');
    }

    public function jsonAction(): JsonResponse
    {
        return new JsonResponse(['name' => 'John Doe']);
    }

    /**
     * Used to embed content as a sub-request.
     */
    public function embeddedAction(): Response
    {
        return new Response('Embedded Content', Response::HTTP_OK);
    }
}
