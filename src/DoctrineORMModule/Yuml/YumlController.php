<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModule\Yuml;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client;
use Zend\Http\Request;

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
        $this->httpClient->setParameterPost(array('dsl_text' => $request->getPost('dsl_text')));
        $response = $this->httpClient->send();

        if (!$response->isSuccess()) {
            throw new \UnexpectedValueException('HTTP Request failed');
        }

        /* @var $redirect \Zend\Mvc\Controller\Plugin\Redirect */
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl('http://yuml.me/' . $response->getBody());
    }
}
