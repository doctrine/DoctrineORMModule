<?php

declare(strict_types=1);

namespace DoctrineORMModule\Yuml;

use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use UnexpectedValueException;

use function assert;

/**
 * Utility to generate Yuml compatible strings from metadata graphs
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
     * @throws UnexpectedValueException if the YUML service answered incorrectly.
     */
    public function indexAction(): Response
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

        return $redirect->toUrl('https://yuml.me/' . $response->getBody());
    }
}
