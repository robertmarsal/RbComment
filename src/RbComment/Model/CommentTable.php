<?php

namespace RbComment\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

class CommentTable
{
    /**
     * @var Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Returns all the comments.
     *
     * @return ResultSet
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();

        return $resultSet;
    }

    /**
     * Returns all the comments of a thread.
     *
     * @param  string    $thread
     * @return ResultSet
     */
    public function fetchAllForThread($thread)
    {
        $select = new Select($this->tableGateway->getTable());
        $select->columns(array('id', 'author', 'content', 'contact',
                               'published_on_raw' => 'published_on',
                               'published_on' => new Expression("DATE_FORMAT(published_on, '%M %d, %Y %H:%i')")))
               ->where(array('thread' => $thread, 'visible' => 1))
               ->order('published_on_raw ASC');

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Returns all the comments pending approval for a thread.
     *
     * @param string $thread
     * @return ResultSet
     */
    public function fetchAllPendingForThread($thread)
    {
        $select = new Select($this->tableGateway->getTable());
        $select->columns(array('id', 'author', 'content', 'contact',
                               'published_on_raw' => 'published_on',
                               'published_on' => new Expression("DATE_FORMAT(published_on, '%M %d, %Y %H:%i')")))
               ->where(array('thread' => $thread, 'visible' => 0))
               ->order('published_on_raw ASC');

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Allow custom comments selection.
     *
     * @param \Zend\Db\Sql\Select $select
     * @return ResultSet
     */
    public function fetchAllUsingSelect(\Zend\Db\Sql\Select $select)
    {
        return $this->tableGateway->selectWith($select);
    }

    /**
     * Returns a comment by id.
     *
     * @param  int                      $id
     * @return \RbComment\Model\Comment
     */
    public function getComment($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();

        return $row;
    }

    /**
     * Saves a comment into the database.
     *
     * @param  \RbComment\Model\Comment $comment
     * @return int                      The id of the inserted/updated comment
     */
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

        $id = (int) $comment->id;
        if ($id === 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getComment($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            }
        }

        return $id;
    }

    /**
     * Removes a comment from the database.
     *
     * @param int $id
     */
    public function deleteComment($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }


    /**
     * Deletes all comments maked as spam from the database.
     *
     * @return int - number of rows affected.
     */
    public function deleteSpam()
    {
        return $this->tableGateway->delete(array('spam' => 1));
    }
}
