<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumDeleteHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AlbumDeleteHandler
    {
        return new AlbumDeleteHandler($container->get(TemplateRendererInterface::class));
    }
}
