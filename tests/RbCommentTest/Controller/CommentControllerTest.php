<?php
namespace RbCommentTest\Controller;

use RbComment\Controller\CommentController;
use RbComment\Model\CommentTable;
use Zend\Http\Request;
use Zend\Mvc\Controller\Plugin\Redirect;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\ServiceManager\ServiceLocatorInterface;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ZendService\Akismet\Akismet;

class CommentControllerTest extends PHPUnit_Framework_TestCase
{
    protected $configMock = [
        'rb_comment' => [
            'strings' => [
                'author'  => 'author',
                'contact' => 'contact',
                'content' => 'content',
                'submit'  => 'submit',
            ],
        ],
    ];

    protected $requestMock;
    protected $serviceLocatorMock;
    protected $commentTableMock;
    protected $akismetServiceMock;

    public function setUp()
    {
        $this->serviceLocatorMock = $this->createMock(ServiceLocatorInterface::class);
        $this->requestMock        = $this->createMock(Request::class);
        $this->commentTableMock   = $this->createMock(CommentTable::class);
        $this->akismetServiceMock = $this->createMock(Akismet::class);

        // Global values
        $_SERVER['HTTP_USER_AGENT'] = 'RbComment Testing Suite';
    }

    public function testAddActionOnlyWorksWithPostMethod()
    {
        // Mocks
        $this->requestMock->expects($this->once())
                          ->method('isPost')
                         ->will($this->returnValue(false));

        $commentControllerMock =
            $this->getMockBuilder(CommentController::class)
                 ->setConstructorArgs([
                     $this->configMock,
                     $this->commentTableMock,
                     $this->akismetServiceMock
                 ])
                 ->setMethods(['getRequest'])
                 ->getMock();

        $commentControllerMock->expects($this->once())
                              ->method('getRequest')
                              ->willReturn($this->requestMock);

        $commentControllerMock->addAction();
    }

    public function testAddActionLogsFormErrorsIntoTheRbCommentNamespace()
    {
        //'contact' key is missing on purpose
        $postMock = [
            'author' => 'Tester',
            'content' => 'test',
            'uri' => '/test',
        ];

        // Request Mock Setup
        $this->requestMock->expects($this->once())
                          ->method('isPost')
                          ->will($this->returnValue(true));

        $this->requestMock->expects($this->once())
                          ->method('getPost')
                          ->will($this->returnValue($postMock));

        // FlashMessenger Mock
        $flashMessengerMock = $this->createMock(FlashMessenger::class);

        $flashMessengerMock->expects($this->once())
                           ->method('setNamespace')
                           ->with('RbComment');

        // Redirect Mock
        $redirectMock = $this->createMock(Redirect::class);

        $redirectMock->expects($this->once())
                     ->method('toUrl')
                     ->with($postMock['uri'] . '#rbcomment');

        // CommentController Mock
        $commentControllerMock =
            $this->getMockBuilder(CommentController::class)
                 ->setConstructorArgs([
                     $this->configMock,
                     $this->commentTableMock,
                     $this->akismetServiceMock
                 ])
                 ->setMethods(['getRequest',  'flashMessenger', 'redirect'])
                 ->getMock();

        $commentControllerMock->expects($this->once())
                              ->method('getRequest')
                              ->will($this->returnValue($this->requestMock));

        $commentControllerMock->expects($this->exactly(2))
                              ->method('flashMessenger')
                              ->will($this->returnValue($flashMessengerMock));

        $commentControllerMock->expects($this->once())
                              ->method('redirect')
                              ->will($this->returnValue($redirectMock));

        $commentControllerMock->addAction();
    }

    /**
     * @dataProvider isSpamDataProvider
     */
    public function testIsSpam($comment, $isSpam)
    {
        $rbCommentConfig = (object) [
            'akismet' => [
                'proxy' => [
                    'use' => false,
                    'trusted' => [],
                    'header' => '',
                ],
            ],
        ];

        $akismetServiceMock =
            $this->getMockBuilder(Akismet::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['isSpam'])
                 ->getMock();

        $akismetServiceMock->expects($this->once())
                           ->method('isSpam')
                           ->will($this->returnValue($isSpam));

        $commentControllerReflection = new ReflectionClass('RbComment\Controller\CommentController');

        $isSpamReflection = $commentControllerReflection->getMethod('isSpam');
        $isSpamReflection->setAccessible(true);

        $commentController = new CommentController(
            $this->configMock,
            $this->commentTableMock,
            $akismetServiceMock
        );

        $this->assertEquals($isSpam, $isSpamReflection->invoke($commentController, $comment, $rbCommentConfig));
    }

    public static function isSpamDataProvider()
    {
        return [
            [
                // comment
                (object) [
                    'author' => 'not a spammer',
                    'contact' => 'me@me.com',
                    'content' => 'test',
                ],
                // isSpam
                false,
            ],
            [
                // comment
                (object) [
                    'author' => 'spammer',
                    'contact' => 'spam@spamfactory.com',
                    'content' => 'spam',
                ],
                // isSpam
                true,
            ],
        ];
    }
}
