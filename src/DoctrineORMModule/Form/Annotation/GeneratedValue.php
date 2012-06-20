<?php

namespace DoctrineORMModule\Form\Annotation;

class GeneratedValue extends AbstractAnnotation
{
    protected $strategy;

    public function initialize($content)
    {
        $data = $this->parseDoctrineAnnotation($content);
        if (isset($data['strategy'])) {
            $this->strategy = strtolower($data['strategy']);
        }
    }

    public function getStrategy()
    {
        return $this->strategy;
    }
}