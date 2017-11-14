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

namespace DoctrineORMModule;

use DoctrineORMModule\Exception\InvalidConfigurationException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  Thomas Dutrion <thomas@engineor.com>
 */
final class EventDispatcherFactory
{
    public function __invoke(ServiceLocatorInterface $container)
    {
        $eventDispatcher = new EventDispatcher();

        $config = $container->get('config');
        $eventListenerConfig = isset($config['doctrine']['cli']['event_dispatcher']['listeners']) ?
            $config['doctrine']['cli']['event_dispatcher']['listeners'] :
            [];
        foreach ($eventListenerConfig as $event => $listener) {
            if (!$container->has($listener)) {
                throw new InvalidConfigurationException(
                    "configuration for doctrine.cli.event_dispatcher.listeners is invalid: Unable to find $listener in container."
                );
            }
            $eventDispatcher->addListener($event, $container->get($listener));
        }

        return $eventDispatcher;
    }
}
