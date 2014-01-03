<?php

namespace RbCommentTest\Form;

use RbComment\Form\CommentForm;
use PHPUnit_Framework_TestCase;

class CommentFormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $formStrings = array(
        'author'  => 'author',
        'contact' => 'contact',
        'content' => 'content',
        'submit'  => 'submit',
    );

    public function testFormConstructorCreatesAllItems()
    {
        $commentForm = new CommentForm($this->formStrings);

        $this->assertTrue($commentForm->has('csrf'));
        $this->assertTrue($commentForm->has('id'));
        $this->assertTrue($commentForm->has('thread'));
        $this->assertTrue($commentForm->has('uri'));
        $this->assertTrue($commentForm->has('author'));
        $this->assertTrue($commentForm->has('contact'));
        $this->assertTrue($commentForm->has('content'));
        $this->assertTrue($commentForm->has('submit'));
    }

    public function testFormAttributesAreSet()
    {
        $commentForm = new CommentForm($this->formStrings);

        $this->assertEquals('post', $commentForm->getAttribute('method'));
        $this->assertEquals('/rbcomment/add', $commentForm->getAttribute('action'));
    }
}
