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

use DoctrineModule\Form\Element;
use Zend\Form\FormElementManager;

return array(
    'aliases' => array(
        'objectselect'        => 'DoctrineModule\Form\Element\ObjectSelect',
        'objectradio'         => 'DoctrineModule\Form\Element\ObjectRadio',
        'objectmulticheckbox' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
    ),
    'factories' => array(
        'DoctrineModule\Form\Element\ObjectSelect' => function (FormElementManager $formElements) {
            $services      = $formElements->getServiceLocator();
            $entityManager = $services->get('Doctrine\ORM\EntityManager');
            $element       = new Element\ObjectSelect;

            $element->setObjectManager($entitymanager);

            return $element;
        },
        'DoctrineModule\Form\Element\ObjectRadio' => function (FormElementManager $formElements) {
            $services      = $formElements->getServiceLocator();
            $entityManager = $services->get('Doctrine\ORM\EntityManager');
            $element       = new Element\ObjectRadio;

            $element->setObjectManager($entitymanager);

            return $element;
        },
        'DoctrineModule\Form\Element\ObjectMultiCheckbox' => function (FormElementManager $formElements) {
            $services      = $formElements->getServiceLocator();
            $entityManager = $services->get('Doctrine\ORM\EntityManager');
            $element       = new Element\ObjectMultiCheckbox;

            $element->setObjectManager($entitymanager);

            return $element;
        },
    ),
);
