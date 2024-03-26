<?php


namespace App\Controller;

use App\Page;
use App\Validation\ValidateForm;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages;

class PageController
{

    private Page $page;
    private array $commonElements;

    /**
     * PageController constructor. Need the page and the page's structural elements.
     * @param Page $page
     * @param array $commonElements
     */
    public function __construct(Page $page, array $commonElements)
    {
        $this->page = $page;
        $this->commonElements = $commonElements;
    }

    /**
     * Magiq method to return the response
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        require APP_PATH . DIRECTORY_SEPARATOR . 'Utils' . DIRECTORY_SEPARATOR .'view_helpers.php';

        if($request->getMethod() === 'POST' && $request->getUri()->getPath() === '/contact') {
            $postedData = $request->getParsedBody();
            $inputs = new ValidateForm($postedData, $request->getUri()->getPath());
            $errors = $inputs->validate();
            $result['values'] = $inputs;
            $result['errors'] = $errors;
            $flash = new Messages();
            if(!is_null($flash->getMessage('success'))) {
                $result = null;
                $result['success'] = $flash->getMessage('success');
            }
            $response->getBody()->write($this->page->render($this->commonElements, $result));
        } else {
            $response->getBody()->write($this->page->render($this->commonElements));
        }
        return $response;
    }

}