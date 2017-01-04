<?php

namespace RbComment\Util;

use RbComment\Model\CommentTable;

trait CommentTableAwareTrait
{
    /**
     * @var CommentTable
     */
    protected $commentTable;

    /**
     * @return CommentTable
     */
    public function getCommentTable()
    {
        if (!$this->commentTable) {
            $sm = $this->getServiceLocator();
            $this->commentTable = $sm->get('RbComment\Model\CommentTable');
        }

        return $this->commentTable;
    }
}
