<?php

namespace RbComment\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Comment implements InputFilterAwareInterface
{
    public $id;
    public $thread;
    public $uri;
    public $author;
    public $contact;
    public $content;
    public $visible;
    public $spam;
    public $published_on;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id       = (isset($data['id'])) ? $data['id'] : null;
        $this->thread   = (isset($data['thread'])) ? $data['thread'] : null;
        $this->uri      = (isset($data['uri'])) ? $data['uri'] : null;
        $this->author   = (isset($data['author'])) ? $data['author'] : null;
        $this->contact  = (isset($data['contact'])) ? $data['contact'] : null;
        $this->content  = (isset($data['content'])) ? $data['content'] : null;
        $this->visible  = (isset($data['visible'])) ? $data['visible'] : 0;
        $this->spam     = (isset($data['spam'])) ? $data['spam'] : 0;
        $this->published_on  = (isset($data['published_on']))
            ? $data['published_on']
            : null;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'thread',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Alnum'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'author',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 150,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'contact',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                    ),
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 200,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'content',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}