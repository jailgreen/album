In this part of the tutorial we will handle the updating and deleting of existing albums. Similar to the album creation action we will create all actions and factories needed. For the update we can reuse the album data form, for deletion we will create a new delete form.

## Add update handler to show form

Next, you need to create the `AlbumUpdateFormHandler.php` file in the existing `/src/App/Handler/` path. Please note the following:

- The `AlbumUpdateFormHandler` has three dependencies to the template renderer, the album repository and the album form. All of these dependencies can be injected with the constructor.
- The `handle()` method is run when the middleware is processed.
    - First the id is taken from the routing to read the current `AlbumEntity` to update.
    - If the form validation was started and failed, then the form has some messages set. In that case an appropriate message is set for the form.
    - If the form validation was not run, then a different message is set for the form. Additionally, the `AlbumEntity` instance is bound to the form. When this is done the form uses the injected hydrator to extract the data from the entity and passes it to the form elements to set their values.
    - Next, the `$data` array is built with the form, the entity and the message.
    - Finally, the update template is rendered and a `HtmlResponse` is passed back.

````php
<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\InputFilter\AlbumInputFilter;
use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumUpdateFormHandler implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     * @var AlbumRepositoryInterface
     */
    private $albumRepository;

    /**
     * @var AlbumInputFilter
     */
    private $inputFilter;

    public function __construct(
        TemplateRendererInterface $renderer,
        AlbumRepositoryInterface $albumRepository,
        AlbumInputFilter $inputFilter
    ) {
        $this->renderer        = $renderer;
        $this->albumRepository = $albumRepository;
        $this->inputFilter     = $inputFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $request->getAttribute('id');

        $album = $this->albumRepository->fetchAlbum($id);

        $message = 'Change album info!';
        if (! empty($this->inputFilter->getMessages())) {
            $message = 'Please check your input!';
        }

        $data = [
            'album'   => $album,
            'message' => $message,
        ];
        
        return new HtmlResponse($this->renderer->render(
            'app::album-update-form',
            $data
        ));
    }
}
````

The corresponding factory will be created in the new `AlbumUpdateFormHandlerFactory.php` file. It looks much similar to the `AlbumCreateFormHandlerFactory` and requests the three dependencies from the DI container to pass them to the constructor of the `AlbumUpdateFormHandler`.

````php
<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\InputFilter\AlbumInputFilter;
use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumUpdateFormHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AlbumUpdateFormHandler
    {
        $template        = $container->get(TemplateRendererInterface::class);
        $albumRepository = $container->get(AlbumUpdateFormHandler::class);
        $inputFilter     = $container->get(AlbumInputFilter::class);
        
        return new AlbumUpdateFormHandler(
            $template, $albumRepository, $inputFilter
        );
    }
}
````

## Create album update middleware

Now you have to create the `AlbumUpdateMiddleware path to handle the update form processing. Please note the following:

- The `AlbumUpdateMiddleware` has three dependencies to the router, the album repository and the album inputFilter. All of these dependencies can be injected with the constructor.
- The `process()`method is run when the form is processed.
    - The `id` is read from the request attributes.
    - The POST data is also read from the request.
    - Then the POST data is passed to the form and the form is validated.
    - If the validation was successful...
        - The `AlbumEntity` is fetched from the repository and the POST data is passed to it.
        - The album is saved and a redirect to the album list is made.
    - If the form validation failed...
        - The next middleware is processed which is the `AlbumUpdateFormHandler` to show the update form.

````php
<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Model\Entity\AlbumEntity;
use App\Model\InputFilter\AlbumInputFilter;
use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;

class AlbumUpdateMiddleware implements MiddlewareInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AlbumRepositoryInterface
     */
    private $albumRepository;

    /**
     * @var AlbumInputFilter
     */
    private $albumInputFilter;

    public function __construct(
        RouterInterface $router,
        AlbumRepositoryInterface $albumRepository,
        AlbumInputFilter $albumInputFilter
    ) {
        $this->router = $router;
        $this->albumRepository = $albumRepository;
        $this->albumInputFilter = $albumInputFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $id       = $request->getAttribute('id');
        $postData = (array)$request->getParsedBody();

        $this->albumInputFilter->setData($postData);

        if ($this->albumInputFilter->isValid()) {
            $postData['id'] = $id;

            /** @var AlbumEntity $album */
            $album = $this->albumRepository->fetchAlbum($id);

            $album->setArtist($postData['artist']);
            $album->setTitle($postData['title']);

            $this->albumRepository->saveAlbum($album);

            return new RedirectResponse(
                $this->router->generateUri('album.list')
            );
        }

        return $handler->handle($request);
    }
}
````

The needed factory will be created in the new `AlbumUpdateMiddlewareFactory.php` file. It looks much similar to the `AlbumCreateMiddlewareFactory` and requests the three needed dependencies from the DI container to pass them to the constructor of the AlbumUpdateMiddleware.

````php
<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Model\InputFilter\AlbumInputFilter;
use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;

class AlbumUpdateMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : AlbumUpdateMiddleware
    {
        $router          = $container->get(RouterInterface::class);
        $albumRepository = $container->get(AlbumRepositoryInterface::class);
        $inputFilter     = $container->get(AlbumInputFilter::class);
        
        return new AlbumUpdateMiddleware($router, $albumRepository, $inputFilter);
    }
}
````

## Create album update template

Now you need to edit the `update.phtml` file in the `/templates/album/` path. In this template you need to setup the form with an form action and display it.

````php
````

## Update album configuration

First, we will update the album configuration in the `ConfigProvider` file to add new middleware actions, a form and some routes.

- In the `dependencies` section four new middleware with its factories are added. These will be created in the next steps.
    - The `AlbumUpdateFormHandler` will show the update form for an album.
    - The `AlbumUpdateMiddleware` will handle the update form processing.
    - The `AlbumDeleteFormHandler` will show the delete form for an album.
    - The `AlbumDeleteMiddleware` will handle the update form processing.
- Additionally, a new delete album form is also registered for the DI Container. The form will be created as well in the next steps.
- The `routes` section for new routes will be added for the four new middleware actions. Some are only processed for GET requests, some only for POST requests.

````php
public function getDependencies() : array
    {
        return [
            'invokables' => [
            ],
            'factories'  => [
                /* ... */
                Handler\AlbumUpdateFormHandler::class => Handler\AlbumUpdateFormHandlerFactory::class,
                Middleware\AlbumUpdateMiddleware::class => Middleware\AlbumUpdateMiddlewareFactory::class,

                /* ... */
            ],
        ];
    }

/* in config.routes.php */

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    /* ... */
    $app->get('/album/update/{id:\d+}', App\Handler\AlbumUpdateFormHandler::class, 'album.update');
    $app->post(
        '/album/update/handle/{id:\d+}',
        [App\Middleware\AlbumUpdateMiddleware::class, App\Handler\AlbumUpdateFormHandler::class],
        'album.update.handle'
    );
};
````

## Add links to the album list page

Next, you need to add a link to the update and the delete page for each album in the album list. Open the `/templates/app/album.list.phtml` and update the `foreach()` loop. You can generate the URLs with the url view helper and display them in the table at the end of each row.

````php
<?php /** @var AlbumEntity $album */ ?>
    <?php foreach ($albumList as $album) : ?>
    <?php
    $urlParams = ['id' => $album->getId()];
    $updateUrl = $this->url('album.update', $urlParams);
    $deleteUrl = $this->url('album.delete', $urlParams);
    ?>
    <tr>
        <td><?= $this->e($album->getId()); ?></td>
        <td><?= $this->e($album->getArtist()); ?></td>
        <td><?= $this->e($album->getTitle()); ?></td>
        <td>
            <a href="<?php echo $updateUrl; ?>" class="btn btn-success">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="<?php echo $deleteUrl; ?>" class="btn btn-success">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
````