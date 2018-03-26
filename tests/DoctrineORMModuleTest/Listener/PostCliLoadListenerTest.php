<?php

namespace DoctrineORMModuleTest\Listener;

use DoctrineORMModule\CliConfigurator;
use DoctrineORMModule\Listener\PostCliLoadListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Zend\EventManager\Event;

/**
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Nicolas Eeckeloo <neeckeloo@gmail.com>
 */
class PostCliLoadListenerTest extends TestCase
{
    public function testListenerConfigureConsoleApplication()
    {
        $application = new Application();
        $event = new Event('loadCli.post', $application);

        $cliConfigurator = $this->getMockBuilder(CliConfigurator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cliConfigurator->expects($this->once())->method('configure')->with($application);

        $listener = new PostCliLoadListener($cliConfigurator);
        $listener($event);
    }
}
