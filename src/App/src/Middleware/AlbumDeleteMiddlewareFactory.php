<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;

class AlbumDeleteMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : AlbumDeleteMiddleware
    {
        return new AlbumDeleteMiddleware();
    }
}
