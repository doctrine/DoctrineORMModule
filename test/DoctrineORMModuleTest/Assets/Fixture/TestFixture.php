<?php

namespace DoctrineORMModuleTest\Assets\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use DoctrineORMModuleTest\Assets\Entity\Test as TestEntity;
use DoctrineORMModuleTest\Assets\Entity\Category;
use DoctrineORMModuleTest\Assets\Entity\Country;

/**
 * Fixture that loads a constant amount of \DoctrineORMModuleTest\Assets\Entity\Test objects into the manager
 */
class TestFixture extends AbstractFixture
{
    /**
     * Number of instances to build when the fixture is loaded
     */
    const INSTANCES_COUNT = 100;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < self::INSTANCES_COUNT; $i += 1) {
            $instance = new TestEntity();
            $instance->setUsername('username');
            $instance->setPassword('password');
            $manager->persist($instance);
        }

        for ($i = 0; $i < self::INSTANCES_COUNT; $i += 1) {
            $instance = new Category();
            $instance->setName('category');
            $manager->persist($instance);
        }

        for ($i = 0; $i < self::INSTANCES_COUNT; $i += 1) {
            $instance = new Country();
            $instance->setName('country');
            $manager->persist($instance);
        }

        $manager->flush();
    }
}
