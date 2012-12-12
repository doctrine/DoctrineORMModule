<?php
namespace DoctrineORMModule\Form\Annotation;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class ElementAnnotationsListener implements ListenerAggregateInterface
{

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Detach listeners
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if (false !== $events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Attach listeners
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleAttributesAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleFilterAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleRequiredAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleTypeAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleValidatorAnnotation'));

        $this->listeners[] = $events->attach('checkForExclude', array($this, 'handleExcludeAnnotation'));
    }

    /**
     * Handle the Attributes annotation
     *
     * Sets the attributes array of the element specification.
     *
     * @param  \Zend\EventManager\EventInterface $e
     * @return void
     */
    public function handleAttributesAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Column) {
            return;
        }

        $elementSpec = $e->getParam('elementSpec');
        switch ($annotation->type) {
            case 'bool':
            case 'boolean':
                $elementSpec['spec']['attributes']['type'] = 'checkbox';
                break;
            case 'text':
                $elementSpec['spec']['attributes']['type'] = 'textarea';
                break;
        }
    }

    /**
     * Handle the AllowEmpty annotation
     *
     * Sets the allow_empty flag on the input specification array.
     *
     * @param  \Zend\EventManager\EventInterface $e
     * @return bool
     */
    public function handleExcludeAnnotation($e)
    {
        $annotations = $e->getParam('annotations');
        foreach ($annotations as $annotation) {
            if ($annotation instanceof GeneratedValue) {
                if ($annotation->strategy == 'AUTO') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Handle the Filter annotation
     *
     * Adds a filter to the filter chain specification for the input.
     *
     * @param  \Zend\EventManager\EventInterface $e
     * @return void
     */
    public function handleFilterAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Column) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        if (!isset($inputSpec['filters'])) {
            $inputSpec['filters'] = array();
        }

        switch ($annotation->type) {
            case 'bool':
            case 'boolean':
                $inputSpec['filters'][] = array('name' => 'Boolean');
                break;
            case 'bigint':
            case 'integer':
            case 'smallint':
                $inputSpec['filters'][] = array('name' => 'Int');
                break;
            case 'datetime':
            case 'datetimetz':
            case 'date':
            case 'time':
            case 'string':
            case 'text':
                $inputSpec['filters'][] = array('name' => 'StringTrim');
                break;
        }
    }

    /**
     * Handle the Required annotation
     *
     * Sets the required flag on the input based on the annotation value.
     *
     * @param  \Zend\EventManager\EventInterface $e
     * @return void
     */
    public function handleRequiredAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Column) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        $inputSpec['required'] = (bool) !$annotation->nullable;
    }

    /**
     * Handle the Type annotation
     *
     * Sets the element class type to use in the element specification.
     *
     * @param  \Zend\EventManager\EventInterface $e
     * @return void
     */
    public function handleTypeAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Column) {
            return;
        }

        $type = $annotation->type;
        switch ($type) {
            case 'bool':
            case 'boolean':
                $type = 'Zend\Form\Element\Checkbox';
                break;
            default:
                $type = 'Zend\Form\Element';
                break;
        }

        $elementSpec = $e->getParam('elementSpec');
        $elementSpec['spec']['type'] = $type;
    }

    /**
     * Handle the Validator annotation
     *
     * Adds a validator to the validator chain of the input specification.
     *
     * @param  \Zend\EventManager\EventInterface $e
     * @return void
     */
    public function handleValidatorAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Column) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        if (!isset($inputSpec['validators'])) {
            $inputSpec['validators'] = array();
        }

        switch ($annotation->type) {
            case 'bool':
            case 'boolean':
                $inputSpec['validators'][] = array(
                    'name' => 'InArray',
                    'options' => array('haystack' => array('0', '1'))
                );
                break;
            case 'float':
                $inputSpec['validators'][] = array('name' => 'Float');
                break;
            case 'bigint':
            case 'integer':
            case 'smallint':
                $inputSpec['validators'][] = array('name' => 'Int');
                break;
            case 'date':
                $inputSpec['validators'][] = array('name' => 'Date');
                break;
            case 'string':
                if ($annotation->length) {
                    $inputSpec['validators'][] = array(
                        'name' => 'StringLength',
                        'options' => array('max' => $annotation->length)
                    );
                }
                break;
        }
    }
}
