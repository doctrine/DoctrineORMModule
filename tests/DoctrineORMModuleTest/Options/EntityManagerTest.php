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

namespace DoctrineORMModuleTest\Collector;

use PHPUnit_Framework_TestCase;
use DoctrineORMModule\Options\EntityManager;

/**
 * Tests for {@see \DoctrineORMModule\Options\EntityManager}
 *
 * @covers \DoctrineORMModule\Options\EntityManager
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class EntityManagerTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetResolver()
    {
        $options = new EntityManager();

        $this->assertSame('doctrine.entity_resolver.orm_default', $options->getEntityResolver());

        $options->setEntityResolver('foo');

        $this->assertSame('doctrine.entity_resolver.foo', $options->getEntityResolver());
    }
}
