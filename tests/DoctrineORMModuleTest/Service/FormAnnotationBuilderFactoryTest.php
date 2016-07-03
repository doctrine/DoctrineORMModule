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

namespace DoctrineORMModuleTest\Service;

use DoctrineORMModule\Service\FormAnnotationBuilderFactory;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for {@see \DoctrineORMModule\Service\FormAnnotationBuilderFactory}
 *
 * @covers \DoctrineORMModule\Service\FormAnnotationBuilderFactory
 */
class FormAnnotationBuilderFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group #352
     */
    public function testFormElementManagerGetsInjected()
    {
        $entityManager      = $this->getMock('Doctrine\ORM\EntityManager', [], [], '', false);
        $formElementManager = $this->getMock('Zend\Form\FormElementManager', [], [], '', false);

        $serviceManager = new ServiceManager();

        $serviceManager->setService('doctrine.entitymanager.test', $entityManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $annotationBuilderFactory = new FormAnnotationBuilderFactory('test');
        $annotationBuilder = $annotationBuilderFactory->createService($serviceManager);

        $this->assertSame($formElementManager, $annotationBuilder->getFormFactory()->getFormElementManager());
    }
}
