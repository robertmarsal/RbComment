<?php

namespace RbComment\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

class CommentTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function fetchAllForThread($thread)
    {
        $select = new Select($this->tableGateway->getTable());
        $select->columns(array('id', 'author', 'content',
                               'published_on' => new Expression("DATE_FORMAT(published_on, '%M %d, %Y %H:%i')")))
               ->where(array('thread' => $thread, 'visible' => 1))
               ->order('published_on DESC');

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    public function getComment($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveComment(Comment $comment)
    {
        $data = array(
            'thread' => $comment->thread,
            'uri' => $comment->uri,
            'author' => $comment->author,
            'contact' => $comment->contact,
            'content' => $comment->content,
            'visible' => $comment->visible,
            'spam' => $comment->spam,
        );

        $id = (int)$comment->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getComment($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }

        return $id;
    }

    public function deleteComment($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}