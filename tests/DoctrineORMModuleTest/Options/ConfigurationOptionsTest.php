<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Options;

use Doctrine\ORM\Mapping\EntityListenerResolver;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use DoctrineORMModule\Options\Configuration;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class ConfigurationOptionsTest extends TestCase
{
    public function testSetGetNamingStrategy() : void
    {
        $options = new Configuration();
        $options->setNamingStrategy(null);
        $this->assertNull($options->getNamingStrategy());

        $options->setNamingStrategy('test');
        $this->assertSame('test', $options->getNamingStrategy());

        $namingStrategy = $this->createMock(NamingStrategy::class);
        $options->setNamingStrategy($namingStrategy);
        $this->assertSame($namingStrategy, $options->getNamingStrategy());

        $this->expectException(InvalidArgumentException::class);
        $options->setNamingStrategy(new stdClass());
    }

    public function testSetGetQuoteStrategy() : void
    {
        $options = new Configuration();
        $options->setQuoteStrategy(null);
        $this->assertNull($options->getQuoteStrategy());

        $options->setQuoteStrategy('test');
        $this->assertSame('test', $options->getQuoteStrategy());

        $quoteStrategy = $this->createMock(QuoteStrategy::class);
        $options->setQuoteStrategy($quoteStrategy);
        $this->assertSame($quoteStrategy, $options->getQuoteStrategy());

        $this->expectException(InvalidArgumentException::class);
        $options->setQuoteStrategy(new stdClass());
    }

    public function testSetRepositoryFactory() : void
    {
        $options = new Configuration();
        $options->setRepositoryFactory(null);
        $this->assertNull($options->getRepositoryFactory());

        $options->setRepositoryFactory('test');
        $this->assertSame('test', $options->getRepositoryFactory());

        $repositoryFactory = new DefaultRepositoryFactory();
        $options->setRepositoryFactory($repositoryFactory);
        $this->assertSame($repositoryFactory, $options->getRepositoryFactory());

        $this->expectException(InvalidArgumentException::class);
        $options->setRepositoryFactory(new stdClass());
    }

    public function testSetGetEntityListenerResolver() : void
    {
        $options = new Configuration();

        $options->setEntityListenerResolver(null);
        $this->assertNull($options->getEntityListenerResolver());

        $options->setEntityListenerResolver('test');
        $this->assertSame('test', $options->getEntityListenerResolver());

        $entityListenerResolver = $this->createMock(EntityListenerResolver::class);

        $options->setEntityListenerResolver($entityListenerResolver);
        $this->assertSame($entityListenerResolver, $options->getEntityListenerResolver());

        $this->expectException(InvalidArgumentException::class);
        $options->setEntityListenerResolver(new stdClass());
    }
}
