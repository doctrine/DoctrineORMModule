<?php

namespace DoctrineORMModule\Form\Annotation;

use DomainException;
use Zend\Form\Annotation\AbstractAnnotation as BaseAnnotation;

abstract class AbstractAnnotation extends BaseAnnotation
{
    /**
     * Initialize
     *
     * @param $content
     */
    public function initialize($content)
    {}

    /**
     * Parses a Doctrine annotation by turning it to JSON and then parsing
     * the JSON.
     *
     * @param string $content
     * @return array
     * @throws \DomainException
     */
    protected function parseDoctrineAnnotation($content)
    {
        $content = sprintf('{%s}',preg_replace('/\s*(\w+)=/', '"$1":', $content));
        $data    = $this->parseJsonContent($content, __METHOD__);

        if (!is_array($data)) {
            throw new DomainException(sprintf(
                '%s expects the annotation to define a JSON object or array; received "%s"',
                __METHOD__,
                gettype($data)
            ));
        }

        return $data;
    }
}