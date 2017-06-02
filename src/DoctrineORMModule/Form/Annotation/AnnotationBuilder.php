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

namespace DoctrineORMModule\Form\Annotation;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Form\Element;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Annotation\AnnotationBuilder as ZendAnnotationBuilder;

/**
 * @author Kyle Spraggs <theman@spiffyjr.me>
 */
class AnnotationBuilder extends ZendAnnotationBuilder
{
    const EVENT_CONFIGURE_FIELD       = 'configureField';
    const EVENT_CONFIGURE_ASSOCIATION = 'configureAssociation';
    const EVENT_EXCLUDE_FIELD         = 'excludeField';
    const EVENT_EXCLUDE_ASSOCIATION   = 'excludeAssociation';

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor. Ensures ObjectManager is present.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        (new ElementAnnotationsListener($this->objectManager))->attach($this->getEventManager());

        return $this;
    }

    /**
     * Overrides the base getFormSpecification() to additionally iterate through each
     * field/association in the metadata and trigger the associated event.
     *
     * This allows building of a form from metadata instead of requiring annotations.
     * Annotations are still allowed through the ElementAnnotationsListener.
     *
     * {@inheritDoc}
     */
    public function getFormSpecification($entity)
    {
        $formSpec     = parent::getFormSpecification($entity);
        $metadata     = $this->objectManager->getClassMetadata(is_object($entity) ? get_class($entity) : $entity);
        $inputFilter  = $formSpec['input_filter'];

        $formElements = [
            Element\ObjectSelect::class,
            Element\ObjectMultiCheckbox::class,
            Element\ObjectRadio::class,
        ];

        foreach ($formSpec['elements'] as $key => $elementSpec) {
            $name          = isset($elementSpec['spec']['name']) ? $elementSpec['spec']['name'] : null;
            $isFormElement = (isset($elementSpec['spec']['type']) &&
                              in_array($elementSpec['spec']['type'], $formElements));

            if (! $name) {
                continue;
            }

            if (! isset($inputFilter[$name])) {
                $inputFilter[$name] = new \ArrayObject();
            }

            $params = [
                'metadata'    => $metadata,
                'name'        => $name,
                'elementSpec' => $elementSpec,
                'inputSpec'   => $inputFilter[$name],
            ];

            if ($this->checkForExcludeElementFromMetadata($metadata, $name)) {
                $elementSpec = $formSpec['elements'];
                unset($elementSpec[$key]);
                $formSpec['elements'] = $elementSpec;

                if (isset($inputFilter[$name])) {
                    unset($inputFilter[$name]);
                }

                $formSpec['input_filter'] = $inputFilter;
                continue;
            }

            if ($metadata->hasField($name) || (! $metadata->hasAssociation($name) && $isFormElement)) {
                $this->getEventManager()->trigger(static::EVENT_CONFIGURE_FIELD, $this, $params);
            } elseif ($metadata->hasAssociation($name)) {
                $this->getEventManager()->trigger(static::EVENT_CONFIGURE_ASSOCIATION, $this, $params);
            }
        }

        $formSpec['options'] = ['prefer_form_input_filter' => true];

        return $formSpec;
    }

    /**
     * @param ClassMetadata $metadata
     * @param $name
     * @return bool
     */
    protected function checkForExcludeElementFromMetadata(ClassMetadata $metadata, $name)
    {
        $params = ['metadata' => $metadata, 'name' => $name];
        $result = false;

        if ($metadata->hasField($name)) {
            $result = $this->getEventManager()->trigger(static::EVENT_EXCLUDE_FIELD, $this, $params);
        } elseif ($metadata->hasAssociation($name)) {
            $result = $this->getEventManager()->trigger(static::EVENT_EXCLUDE_ASSOCIATION, $this, $params);
        }

        if ($result) {
            $result = (bool)$result->last();
        }

        return $result;
    }
}
