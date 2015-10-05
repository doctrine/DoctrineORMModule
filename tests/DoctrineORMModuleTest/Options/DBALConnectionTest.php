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

use PHPUnit_Framework_TestCase;
use DoctrineORMModule\Options\DBALConnection;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;

/**
 * @covers \DoctrineORMModule\Options\DBALConnection
 */
class DBALConnectionTest extends PHPUnit_Framework_TestCase
{
    public function testSetNullCommentedTypes()
    {
        $options = new DBALConnection();
        $options->setDoctrineCommentedTypes([]);
        $this->assertSame(array(), $options->getDoctrineCommentedTypes());
    }

    public function testSetGetCommentedTypes()
    {
        $options = new DBALConnection();
        $options->setDoctrineCommentedTypes(array('mytype'));
        $this->assertSame(array('mytype'), $options->getDoctrineCommentedTypes());
    }
}
