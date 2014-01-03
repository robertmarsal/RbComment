<?php

namespace RbCommentTest\Mvc\Controller\Plugin;

use RbComment\View\Helper\Comment as RbCommentViewHelper;
use PHPUnit_Framework_TestCase;

class CommentTest extends PHPUnit_Framework_TestCase
{
    public function testSetAndGetServiceLocator()
    {
        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface',
            array(), array(), 'ServiceLocatorInterface');

        $commentViewHelper = new RbCommentViewHelper();
        $commentViewHelper->setServiceLocator($serviceLocatorMock);

        $this->assertEquals($serviceLocatorMock, $commentViewHelper->getServiceLocator());
    }
}
