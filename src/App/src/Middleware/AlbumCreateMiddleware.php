<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Model\InputFilter\AlbumInputFilter;
use App\Model\Entity\AlbumEntity;
use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;

class AlbumCreateMiddleware implements MiddlewareInterface
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
        $postData = (array)$request->getParsedBody();
        $this->albumInputFilter->setData($postData);

        if ($this->albumInputFilter->isValid()) {
            $album = new AlbumEntity();
            $album->setArtist($postData['artist']);
            $album->setTitle($postData['title']);

            if ($this->albumRepository->saveAlbum($album)) {
                return new RedirectResponse(
                    $this->router->generateUri('album.list')
                );
            }
        }

        return $handler->handle($request);
    }
}
