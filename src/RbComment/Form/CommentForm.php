<?php
namespace RbComment\Form;

use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct(array $strings)
    {
        parent::__construct('rbcomment');

        $this->setAttributes([
            'method' => 'post',
            'action' => '/rbcomment/add',
        ]);

        $this->add([
            'type' => 'Csrf',
            'name' => 'csrf',
        ]);

        $this->add([
            'name' => 'id',
            'attributes' => [
                'type'  => 'hidden',
            ],
        ]);

        $this->add([
            'name' => 'thread',
            'attributes' => [
                'type'  => 'hidden',
            ],
        ]);

        $this->add([
            'name' => 'uri',
            'attributes' => [
                'type'  => 'hidden',
            ],
        ]);

        $this->add([
            'name' => 'author',
            'attributes' => [
                'type'        => 'text',
                'placeholder' => $strings['author'],
            ],
        ]);

        $this->add([
            'name' => 'contact',
            'attributes' => [
                'type'        => 'text',
                'placeholder' => $strings['contact'],
            ],
        ]);

        $this->add([
            'type' => 'Textarea',
            'name' => 'content',
            'attributes' => [
                'placeholder' => $strings['content'],
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => $strings['submit'],
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
