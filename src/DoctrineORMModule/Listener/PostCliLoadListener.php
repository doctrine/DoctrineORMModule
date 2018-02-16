<?php

namespace DoctrineORMModule\Listener;

use DoctrineORMModule\CliConfigurator;
use Symfony\Component\Console\Helper\DialogHelper;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  Nicolas Eeckeloo <neeckeloo@gmail.com>
 */
class PostCliLoadListener extends AbstractListenerAggregate
{
    /**
     * @var CliConfigurator
     */
    private $cliConfigurator;

    public function __construct(CliConfigurator $cliConfigurator)
    {
        $this->cliConfigurator = $cliConfigurator;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->getSharedManager()->attach('doctrine', 'loadCli.post', $this, $priority);
    }

    public function __invoke(EventInterface $event)
    {
        /* @var $cli \Symfony\Component\Console\Application */
        $cli = $event->getTarget();

        $this->cliConfigurator->configure($cli);
    }
}
