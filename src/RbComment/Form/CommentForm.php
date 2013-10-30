<?php

namespace RbComment\Form;

use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct(array $strings)
    {
        parent::__construct('rbcomment');

        $this->setAttributes(array(
            'method' => 'post',
            'action' => '/rbcomment/add',
        ));

        $this->add(array(
            'type' => 'Csrf',
            'name' => 'csrf',
        ));

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'thread',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'uri',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'author',
            'attributes' => array(
                'type'  => 'text',
                'placeholder' => $strings['author'],
            ),
        ));

        $this->add(array(
            'name' => 'contact',
            'attributes' => array(
                'type'  => 'text',
                'placeholder' => $strings['contact'],
            ),
        ));

        $this->add(array(
            'type' => 'Textarea',
            'name' => 'content',
            'attributes' => array(
                'placeholder' => $strings['content'],
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => $strings['submit'],
                'id' => 'submitbutton',
            ),
        ));
    }
}
