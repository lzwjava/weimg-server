<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 16/2/10
 * Time: 下午3:47
 */
class CommentDao extends BaseDao
{
    function addComment($postId, $parentId, $content, $authorId)
    {
        $data = array(
            KEY_POST_ID => $postId,
            KEY_CONTENT => $content,
            KEY_AUTHOR_ID => $authorId
        );
        if ($parentId != null) {
            $data[KEY_PARENT_ID] = $parentId;
        }
        $this->db->insert(TABLE_COMMENTS, $data);
        return $this->db->insert_id();
    }

    private function getVote($userId, $commentId)
    {
        $sql = "SELECT vote FROM comment_votes WHERE userId=? AND commentId=?";
        $value = array($userId, $commentId);
        $row = $this->db->query($sql, $value)->row();
        return $row ? $row->vote : null;
    }

    private function addVote($userId, $commentId, $vote)
    {
        $data = array(
            KEY_USER_ID => $userId,
            KEY_COMMENT_ID => $commentId,
            KEY_VOTE => $vote
        );
        $this->db->insert(TABLE_COMMENT_VOTES, $data);
        return $this->db->insert_id();
    }

    private function deleteVote($userId, $commentId)
    {
        $this->db->delete(TABLE_COMMENT_VOTES, array(
            KEY_USER_ID => $userId,
            KEY_COMMENT_ID => $commentId
        ));
    }

    private function updateVote($userId, $commentId, $vote)
    {
        $data = array(KEY_VOTE => $vote);
        $where = array(
            KEY_USER_ID => $userId,
            KEY_COMMENT_ID => $commentId
        );
        $this->db->update(TABLE_COMMENT_VOTES, $data, $where);
    }

    function voteComment($userId, $commentId, $newVote)
    {
        $this->db->trans_start();
        $vote = $this->getVote($userId, $commentId);
        if ($vote == null) {
            $this->addVote($userId, $commentId, $newVote);
        } else if ($vote == $newVote) {
            $this->deleteVote($userId, $commentId);
        } else {
            $this->updateVote($userId, $commentId, $newVote);
        }
        $this->db->trans_complete();
    }

}