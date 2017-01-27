<?php
namespace RbComment\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Comment implements InputFilterAwareInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $thread;

    /**
     * @var string
     */
    public $uri;

    /**
     * @var string
     */
    public $author;

    /**
     * @var string
     */
    public $contact;

    /**
     *
     * @var string
     */
    public $content;

    /**
     * @var boolean
     */
    public $visible;

    /**
     * @var boolean
     */
    public $spam;

    /**
     * @var timestamp
     */
    public $published_on;

    /**
     * @var InputFilter
     */
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

            $inputFilter->add($factory->createInput([
                'name'     => 'id',
                'required' => true,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]));

            $inputFilter->add($factory->createInput([
                'name'     => 'thread',
                'required' => true,
                'filters'  => [
                    ['name' => 'Alnum'],
                ],
            ]));

            $inputFilter->add($factory->createInput([
                'name'     => 'author',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 150,
                        ],
                    ],
                ],
            ]));

            $inputFilter->add($factory->createInput([
                'name'     => 'contact',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                    ],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 200,
                        ],
                    ],
                ],
            ]));

            $inputFilter->add($factory->createInput([
                'name'     => 'content',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
