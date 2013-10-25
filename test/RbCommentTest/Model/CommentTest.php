<?php

namespace RbCommentTest\Model;

use RbComment\Model\Comment;
use PHPUnit_Framework_TestCase;

class CommentTest extends PHPUnit_Framework_TestCase
{
    public function testCommentInitialState()
    {
        $comment = new Comment();

        $this->assertNull($comment->id);
        $this->assertNull($comment->thread);
        $this->assertNull($comment->uri);
        $this->assertNull($comment->author);
        $this->assertNull($comment->contact);
        $this->assertNull($comment->content);
        $this->assertNull($comment->visible);
        $this->assertNull($comment->published_on);
    }
}