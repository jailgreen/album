<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace App\Handler;

use App\Model\InputFilter\AlbumInputFilter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumCreateFormHandler implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     *
     * @var AlbumInputFilter
     */
    private $inputFilter;

    public function __construct(TemplateRendererInterface $renderer, AlbumInputFilter $inputFilter)
    {
        $this->renderer    = $renderer;
        $this->inputFilter = $inputFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $message = 'Enter new album info!';
        if (! empty($this->inputFilter->getMessages())) {
            $message = 'Please check your input!';
        }

        $data = [ 
            'message' => $message,
        ];
        
        return new HtmlResponse($this->renderer->render(
            'app::album-create-form',
            $data
        ));
    }
}
