<?php

namespace DoctrineORMModule\Form\Annotation;

class Column extends AbstractAnnotation
{
    protected $type;

    protected $length;

    protected $nullable;

    public function initialize($content)
    {
        $data = $this->parseDoctrineAnnotation($content);
        if (isset($content['type'])) {
            $this->type = strtolower($data['type']);
        }
        if (isset($data['length'])) {
            $this->length = strtolower($data['length']);
        }
        if (isset($data['nullable'])) {
            $this->nullable = strtolower($data['nullable']);
        }
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getNullable()
    {
        return $this->nullable;
    }

    public function getType()
    {
        return $this->type;
    }
}