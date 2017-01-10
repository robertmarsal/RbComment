<?php

namespace RbCommentTest\Model;

use RbComment\Model\Comment;
use Zend\InputFilter\InputFilter;
use PHPUnit_Framework_TestCase;

class CommentTest extends PHPUnit_Framework_TestCase
{
    protected $testComment;
    protected $testArray;

    public function setUp()
    {
        $this->testComment = new Comment();

        $this->testArray = [
            'id' => 1,
            'thread' => 12345,
            'uri' => 'test-uri',
            'author' => 'Robert Boloc',
            'contact' => 'contact@robertboloc.eu',
            'content' => 'This is a test comment',
            'visible' => 1,
            'spam' => 0,
            'published_on' => 12345678,
        ];
    }

    public function testCommentInitialState()
    {
        $this->assertNull($this->testComment->id);
        $this->assertNull($this->testComment->thread);
        $this->assertNull($this->testComment->uri);
        $this->assertNull($this->testComment->author);
        $this->assertNull($this->testComment->contact);
        $this->assertNull($this->testComment->content);
        $this->assertNull($this->testComment->visible);
        $this->assertNull($this->testComment->spam);
        $this->assertNull($this->testComment->published_on);
    }

    public function testExchangeArrayExchangesTheValues()
    {
        $this->testComment->exchangeArray($this->testArray);

        $this->assertEquals($this->testArray['id'], $this->testComment->id);
        $this->assertEquals($this->testArray['thread'], $this->testComment->thread);
        $this->assertEquals($this->testArray['uri'], $this->testComment->uri);
        $this->assertEquals($this->testArray['author'], $this->testComment->author);
        $this->assertEquals($this->testArray['contact'], $this->testComment->contact);
        $this->assertEquals($this->testArray['content'], $this->testComment->content);
        $this->assertEquals($this->testArray['visible'], $this->testComment->visible);
        $this->assertEquals($this->testArray['spam'], $this->testComment->spam);
        $this->assertEquals($this->testArray['published_on'], $this->testComment->published_on);
    }

    public function testToArrayReturnsAllTheValues()
    {
        $this->testComment->exchangeArray($this->testArray);

        $resultArray = $this->testComment->toArray();

        $this->assertEquals($this->testArray['id'], $resultArray['id']);
        $this->assertEquals($this->testArray['thread'], $resultArray['thread']);
        $this->assertEquals($this->testArray['uri'], $resultArray['uri']);
        $this->assertEquals($this->testArray['author'], $resultArray['author']);
        $this->assertEquals($this->testArray['contact'], $resultArray['contact']);
        $this->assertEquals($this->testArray['content'], $resultArray['content']);
        $this->assertEquals($this->testArray['visible'], $resultArray['visible']);
        $this->assertEquals($this->testArray['spam'], $resultArray['spam']);
        $this->assertEquals($this->testArray['published_on'], $resultArray['published_on']);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Not used
     */
    public function testSetInputFilterThrowsExceptionWhenInvoked()
    {
        $this->testComment->setInputFilter(new InputFilter());
    }

    /**
     * @dataProvider inputFilterDataProvider
     */
    public function testGetInputFilterValidation($data, $validity)
    {
        $inputFilter = $this->testComment->getInputFilter();
        $inputFilter->setData($data);

        $this->assertEquals($inputFilter->isValid(), $validity);
    }

    public static function inputFilterDataProvider()
    {
        return [
            // False because no data is sent
            [
                [],
                false,
            ],
            // True because everything is ok
            [
                [
                    'id' => 1,
                    'thread' => md5('test'),
                    'author' => 'Robert Boloc',
                    'contact' => 'robert@test.com',
                    'content' => 'bla bla bla',
                ],
                true,
            ],
        ];
    }
}
