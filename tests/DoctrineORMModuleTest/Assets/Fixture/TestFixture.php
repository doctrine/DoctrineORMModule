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

        for ($i = 0 ; $i < self::INSTANCES_COUNT ; $i += 1) {
            $instance = new Category();
            $instance->setName('category');
            $manager->persist($instance);
        }

        for ($i = 0 ; $i < self::INSTANCES_COUNT ; $i += 1) {
            $instance = new Country();
            $instance->setName('country');
            $manager->persist($instance);
        }

        $manager->flush();
    }
}
