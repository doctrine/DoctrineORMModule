<?php

namespace DoctrineORMModuleTest\Options;

use PHPUnit\Framework\TestCase;
use DoctrineORMModule\Options\Configuration;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;

class ConfigurationOptionsTest extends TestCase
{
    public function testSetGetNamingStrategy()
    {
        $options = new Configuration();
        $options->setNamingStrategy(null);
        $this->assertNull($options->getNamingStrategy());

        $options->setNamingStrategy('test');
        $this->assertSame('test', $options->getNamingStrategy());

        $namingStrategy = $this->createMock(\Doctrine\ORM\Mapping\NamingStrategy::class);
        $options->setNamingStrategy($namingStrategy);
        $this->assertSame($namingStrategy, $options->getNamingStrategy());

        $this->expectException(\Laminas\Stdlib\Exception\InvalidArgumentException::class);
        $options->setNamingStrategy(new \stdClass());
    }

    public function testSetGetQuoteStrategy()
    {
        $options = new Configuration();
        $options->setQuoteStrategy(null);
        $this->assertNull($options->getQuoteStrategy());

        $options->setQuoteStrategy('test');
        $this->assertSame('test', $options->getQuoteStrategy());

        $quoteStrategy = $this->createMock(\Doctrine\ORM\Mapping\QuoteStrategy::class);
        $options->setQuoteStrategy($quoteStrategy);
        $this->assertSame($quoteStrategy, $options->getQuoteStrategy());

        $this->expectException(\Laminas\Stdlib\Exception\InvalidArgumentException::class);
        $options->setQuoteStrategy(new \stdClass());
    }

    public function testSetRepositoryFactory()
    {
        $options = new Configuration();
        $options->setRepositoryFactory(null);
        $this->assertNull($options->getRepositoryFactory());

        $options->setRepositoryFactory('test');
        $this->assertSame('test', $options->getRepositoryFactory());

        $repositoryFactory = new DefaultRepositoryFactory();
        $options->setRepositoryFactory($repositoryFactory);
        $this->assertSame($repositoryFactory, $options->getRepositoryFactory());

        $this->expectException(\Laminas\Stdlib\Exception\InvalidArgumentException::class);
        $options->setRepositoryFactory(new \stdClass());
    }

    public function testSetGetEntityListenerResolver()
    {
        $options = new Configuration();

        $options->setEntityListenerResolver(null);
        $this->assertNull($options->getEntityListenerResolver());

        $options->setEntityListenerResolver('test');
        $this->assertSame('test', $options->getEntityListenerResolver());

        $entityListenerResolver = $this->createMock(\Doctrine\ORM\Mapping\EntityListenerResolver::class);

        $options->setEntityListenerResolver($entityListenerResolver);
        $this->assertSame($entityListenerResolver, $options->getEntityListenerResolver());

        $this->expectException(\Laminas\Stdlib\Exception\InvalidArgumentException::class);
        $options->setEntityListenerResolver(new \stdClass());
    }
}
