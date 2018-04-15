<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Zend\Expressive\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/', App\Handler\AlbumListHandler::class, 'album.list');
    $app->get('/album/create', App\Handler\AlbumCreateFormHandler::class, 'album.create');
    $app->post(
        '/album/create/handle',
        [App\Middleware\AlbumCreateMiddleware::class, App\Handler\AlbumCreateFormHandler::class],
        'album.create.handle'
    );
    $app->get('/album/update/{id:\d+}', App\Handler\AlbumUpdateFormHandler::class, 'album.update');
    $app->post(
        '/album/update/handle/{id:\d+}',
        [App\Middleware\AlbumUpdateMiddleware::class, App\Handler\AlbumUpdateFormHandler::class],
        'album.update.handle'
    );
};
