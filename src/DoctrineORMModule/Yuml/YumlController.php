<?php

namespace DoctrineORMModule\Yuml;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Utility to generate Yuml compatible strings from metadata graphs
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class YumlController extends AbstractActionController
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Redirects the user to a YUML graph drawn with the provided `dsl_text`
     *
     * @return \Zend\Http\Response
     *
     * @throws \UnexpectedValueException if the YUML service answered incorrectly
     */
    public function indexAction()
    {
        /* @var $request \Zend\Http\Request */
        $request = $this->getRequest();
        $this->httpClient->setMethod(Request::METHOD_POST);
        $this->httpClient->setParameterPost(['dsl_text' => $request->getPost('dsl_text')]);
        $response = $this->httpClient->send();

        if (! $response->isSuccess()) {
            throw new \UnexpectedValueException('HTTP Request failed');
        }

        /* @var $redirect \Zend\Mvc\Controller\Plugin\Redirect */
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl('https://yuml.me/' . $response->getBody());
    }
}
