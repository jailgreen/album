<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

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

        $album = $this->albumRepository->fetchAlbum((int) $id);

        $message = 'Change album info!';
        if (! empty($this->inputFilter->getMessages())) {
            $message = 'Please check your input!';
            var_dump($this->inputFilter->getMessages());
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
