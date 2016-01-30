<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 16/1/29
 * Time: 下午5:05
 */
class PostDao extends BaseDao
{
    private $scoreHelper;

    function __construct()
    {
        parent::__construct();
        $this->load->helper('score');
        $this->scoreHelper = new ScoreHelper();
    }

    function addPost($imageIds, $title, $author, $topic)
    {
        $this->db->trans_start();
        $date = new DateTime();
        $hot = $this->scoreHelper->hot(0, 0, $date);
        $created = $date->format('Y-m-d H:i:s');
        $data = array(
            KEY_TITLE => $title,
            KEY_TOPIC => $topic,
            KEY_AUTHOR => $author,
            KEY_CREATED => $created,
            KEY_SCORE => $hot
        );
        $this->db->insert(TABLE_POSTS, $data);
        $insertId = $this->db->insert_id();
        foreach ($imageIds as $id) {
            $postImage = array(
                KEY_POST_ID => $insertId,
                KEY_IMAGE_ID => $id
            );
            $this->db->insert(TABLE_POST_IMAGES, $postImage);
        }
        $this->db->trans_complete();
        return $insertId;
    }

    private function publicFields()
    {
        return $this->mergeFields(array(
            KEY_POST_ID,
            KEY_TITLE,
            KEY_TOPIC,
            KEY_SCORE,
            KEY_CREATED
        ), TABLE_POSTS);
    }

    function getPost($postId)
    {
        $fields = $this->publicFields();
        $sql = "SELECT $fields FROM posts LEFT JOIN post_images USING(postId)
                LEFT JOIN images USING(imageId) WHERE postId=? GROUP BY postId";
        return $this->db->query($sql, array($postId))->row();
    }

    function getPostList($skip, $limit)
    {
        $sql = "SELECT * FROM posts LEFT JOIN post_images USING(postId)
                LEFT JOIN images USING(imageId) GROUP BY postId
                order by score DESC limit $limit offset $skip";
        return $this->db->query($sql)->result();
    }

    private function getVote($userId, $postId)
    {
        $sql = "SELECT vote FROM post_votes WHERE userId=? AND postId=?";
        $value = array($userId, $postId);
        $row = $this->db->query($sql, $value)->row();
        return $row ? $row->vote : null;
    }

    private function addVote($userId, $postId, $vote)
    {
        $data = array(
            KEY_USER_ID => $userId,
            KEY_POST_ID => $postId,
            KEY_VOTE => $vote
        );
        $this->db->insert(TABLE_POST_VOTES, $data);
        return $this->db->insert_id();
    }

    private function deleteVote($userId, $postId)
    {
        $this->db->delete(TABLE_POST_VOTES, array(
            KEY_USER_ID => $userId,
            KEY_POST_ID => $postId
        ));
    }

    private function updateVote($userId, $postId, $vote)
    {
        $data = array(KEY_VOTE => $vote);
        $where = array(
            KEY_USER_ID => $userId,
            KEY_POST_ID => $postId
        );
        $this->db->update(TABLE_POST_VOTES, $data, $where);
    }

    private function updateScore($postId)
    {
        $sql = "SELECT created, count(post_votes.vote = 'up') AS ups, count(post_votes.vote ='down') AS downs
                FROM posts LEFT JOIN post_votes USING(postId)
                WHERE postId=? GROUP BY postId";
        $values = array($postId);
        $post = $this->db->query($sql, $values)->row();
        $date = new DateTime($post->created);
        logInfo("post " . json_encode($post));
        $hot = $this->scoreHelper->hot($post->ups, $post->downs, $date);
        $this->db->update(TABLE_POSTS, array(KEY_SCORE => $hot));
    }

    function votePost($userId, $postId, $newVote)
    {
        $this->db->trans_start();
        $vote = $this->getVote($userId, $postId);
        if ($vote == null) {
            $this->addVote($userId, $postId, $newVote);
        } else if ($vote == $newVote) {
            $this->deleteVote($userId, $postId);
        } else {
            $this->updateVote($userId, $postId, $newVote);
        }
        $this->updateScore($postId);
        $this->db->trans_complete();
    }
}