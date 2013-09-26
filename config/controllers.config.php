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

use DoctrineORMModule\Yuml\YumlController;
use Zend\Http\Client;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

return array(
    'factories' => array(
        // Yuml controller, used to generate Yuml graphs since
        // yuml.me doesn't do redirects on its own
        'DoctrineORMModule\\Yuml\\YumlController'  => function (AbstractPluginManager $pluginManager) {
            $config = $pluginManager->getServiceLocator()->get('Config');

            if (! isset($config['zenddevelopertools']['toolbar']['enabled'])
                || !$config['zenddevelopertools']['toolbar']['enabled']
            ) {
                throw new ServiceNotFoundException(
                    'Service DoctrineORMModule\\Yuml\\YumlController could not be found'
                );
            }

            return new YumlController(
                new Client('http://yuml.me/diagram/class/', array('timeout' => 30))
            );
        },
    ),
);
