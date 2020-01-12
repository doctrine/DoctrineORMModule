<?php

declare(strict_types=1);

namespace DoctrineORMModule\Yuml;

use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Controller\Plugin\Redirect;
use UnexpectedValueException;
use function assert;

/**
 * Utility to generate Yuml compatible strings from metadata graphs
 *
 * @link    http://www.doctrine-project.org/
 */
class YumlController extends AbstractActionController
{
    protected Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Redirects the user to a YUML graph drawn with the provided `dsl_text`
     *
     * If the YUML service answered incorrectly throws exception
     *
     * @throws UnexpectedValueException
     */
    public function indexAction() : Response
    {
        $request = $this->getRequest();
        assert($request instanceof Request);
        $this->httpClient->setMethod(Request::METHOD_POST);
        $this->httpClient->setParameterPost(['dsl_text' => $request->getPost('dsl_text')]);
        $response = $this->httpClient->send();

        if (! $response->isSuccess()) {
            throw new UnexpectedValueException('HTTP Request failed');
        }

        $redirect = $this->plugin('redirect');
        assert($redirect instanceof Redirect);

        return $redirect->toUrl('https://yuml.me/' . $response->getBody());
    }
}
