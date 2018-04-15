<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'doctrine'     => $this->getDoctrine(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'invokables' => [
            ],
            'factories'  => [
                Handler\AlbumListHandler::class => Handler\AlbumListHandlerFactory::class,
                Handler\AlbumCreateFormHandler::class => Handler\AlbumCreateFormHandlerFactory::class,
                Middleware\AlbumCreateMiddleware::class => Middleware\AlbumCreateMiddlewareFactory::class,
                Handler\AlbumUpdateFormHandler::class => Handler\AlbumUpdateFormHandlerFactory::class,
                Middleware\AlbumUpdateMiddleware::class => Middleware\AlbumUpdateMiddlewareFactory::class,

                Model\InputFilter\AlbumInputFilter::class => Model\InputFilter\AlbumInputFilterFactory::class,

                Model\Repository\AlbumRepositoryInterface::class =>
                    Model\Repository\AlbumRepositoryFactory::class,

                Model\Storage\StorageInterface::class =>
                    Db\ORMStorageFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates() : array
    {
        return [
            'paths' => [
                'app'    => ['templates/app'],
            ],
        ];
    }

    /**
     * Returns doctrine configuration
     * @return array
     */
    public function getDoctrine() : array
    {
        return [
            'connection' => [
                'orm_default' => [
                    'params' => [
                        'url' => 'mysql://jukka:jukka_db@localhost/jukka_db',
                    ],
                ],
            ],
            'driver' => [
                'orm_default' => [
                    'class' => \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain::class,
                    'drivers' => [
                        'App\Model\Entity' => 'app_entity',
                    ],
                ],
                'app_entity' => [
                    'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => __DIR__ . '/Model/Entity',
                ],
            ],
        ];
    }
}
