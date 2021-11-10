<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use DoctrineORMModuleTest\Assets\Entity\Category;
use DoctrineORMModuleTest\Assets\Entity\Country;
use DoctrineORMModuleTest\Assets\Entity\Test as TestEntity;

/**
 * Fixture that loads a constant amount of \DoctrineORMModuleTest\Assets\Entity\Test objects into the manager
 */
class TestFixture extends AbstractFixture
{
    /**
     * Number of instances to build when the fixture is loaded
     */
    public const INSTANCES_COUNT = 100;

    public function load(ObjectManager $manager): void
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
