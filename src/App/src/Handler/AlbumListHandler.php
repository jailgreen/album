<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Handler;

use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumListHandler implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     *
     * @var AlbumRepositoryInterface
     */
    private $albumRepository;

    public function __construct(
        TemplateRendererInterface $renderer,
        AlbumRepositoryInterface $albumRepository
    ){
        $this->renderer        = $renderer;
        $this->albumRepository = $albumRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $data = [
            'albumList' => $this->albumRepository->fetchAlbumList(),
        ];

        return new HtmlResponse($this->renderer->render(
            'app::album-list',
            $data
        ));
    }
}
