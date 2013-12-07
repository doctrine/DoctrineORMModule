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

namespace DoctrineORMModuleTest\Options;

use PHPUnit_Framework_TestCase as TestCase;
use DoctrineORMModule\Options\Configuration;

class ConfigurationOptionsTest extends TestCase
{
    public function testSetGetNamingStrategy()
    {
        $options = new Configuration();
        $options->setNamingStrategy(null);
        $this->assertNull($options->getNamingStrategy());

        $options->setNamingStrategy('test');
        $this->assertSame('test', $options->getNamingStrategy());

        $namingStrategy = $this->getMock('Doctrine\ORM\Mapping\NamingStrategy');
        $options->setNamingStrategy($namingStrategy);
        $this->assertSame($namingStrategy, $options->getNamingStrategy());

        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException');
        $options->setNamingStrategy(new \stdClass());
    }

    public function testSetRepositoryFactory()
    {
        $options = new Configuration();
        $options->setRepositoryFactory(null);
        $this->assertNull($options->getRepositoryFactory());

        $options->setRepositoryFactory('test');
        $this->assertSame('test', $options->getRepositoryFactory());

        $repositoryFactory = $this->getMock('Doctrine\ORM\Repository\DefaultRepositoryFactory');
        $options->setRepositoryFactory($repositoryFactory);
        $this->assertSame($repositoryFactory, $options->getRepositoryFactory());

        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException');
        $options->setRepositoryFactory(new \stdClass());
    }
}
