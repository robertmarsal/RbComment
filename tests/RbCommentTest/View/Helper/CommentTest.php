<?php
namespace RbCommentTest\Mvc\Controller\Plugin;

use RbComment\Model\CommentTable;
use RbComment\View\Helper\Comment as RbCommentViewHelper;
use PHPUnit_Framework_TestCase;

class CommentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group viewhelper
     */
    public function testConstructStoresTheDependencies()
    {
        $viewHelperManagerMock = $this->getMockBuilder('ViewHelperManager')->getMock();
        $routerServiceMock     = $this->getMockBuilder('Router')->getMock();
        $configServiceMock     = [uniqid()];
        $commentTableMock      = $this->createMock(CommentTable::class);

        $commentViewHelper = new RbCommentViewHelper(
            $viewHelperManagerMock,
            $routerServiceMock,
            $configServiceMock,
            $commentTableMock
        );

        $this->assertAttributeEquals($viewHelperManagerMock, 'viewHelperManager', $commentViewHelper);
        $this->assertAttributeEquals($routerServiceMock, 'routerService', $commentViewHelper);
        $this->assertAttributeEquals($configServiceMock, 'configService', $commentViewHelper);
        $this->assertAttributeEquals($commentTableMock, 'commentTable', $commentViewHelper);
    }
}
