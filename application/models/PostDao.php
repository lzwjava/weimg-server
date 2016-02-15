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
    private $imageDao;

    function __construct()
    {
        parent::__construct();
        $this->load->helper('score');
        $this->load->model('imageDao');
        $this->scoreHelper = new ScoreHelper();
        $this->imageDao = new ImageDao();
    }

    function addPost($imageIds, $title, $author, $topic)
    {
        $this->db->trans_start();
        $date = new DateTime();
        $hot = $this->scoreHelper->hot(0, 0, $date);
        $created = $date->format('Y-m-d H:i:s');
        $cover = $imageIds[0];
        $data = array(
            KEY_TITLE => $title,
            KEY_TOPIC => $topic,
            KEY_AUTHOR => $author,
            KEY_CREATED => $created,
            KEY_SCORE => $hot,
            KEY_COVER => $cover
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
            KEY_COVER,
            KEY_AUTHOR,
            KEY_CREATED
        ), TABLE_POSTS);
    }

    function getPost($postId)
    {
        $fields = $this->publicFields();
        $sql = "SELECT $fields,count(CASE WHEN vote='up' THEN 1 END) AS ups,
                count(CASE WHEN vote='down' THEN 1 END) AS downs,
                i.imageId,i.link,i.width,i.height,
                u.userId, u.username,
                count(commentId) as commentCount
                FROM posts LEFT JOIN post_images USING(postId)
                LEFT JOIN images as i  ON i.imageId = posts.cover
                LEFT JOIN post_votes USING(postId)
                LEFT JOIN comments as c USING(postId)
                LEFT JOIN users as u on u.userId = posts.author
                WHERE postId=? GROUP BY postId";
        $post = $this->db->query($sql, array($postId))->row();
        $this->setPostImages($post);
        if ($post != null) {
            $this->handlePosts(array($post));
        }
        return $post;
    }

    function setPostImages($post)
    {
        if ($post == null) {
            return $post;
        }
        $fields = $this->imageDao->publicFields();
        $sql = "select $fields from post_images left join images using(imageId) where postId=?";
        $binds = array($post->postId);
        $images = $this->db->query($sql, $binds)->result();
        $post->images = $images;
    }

    function getPostList($skip, $limit)
    {
        $fields = $this->publicFields();
        $sql = "SELECT $fields,count(CASE WHEN pv.vote='up' THEN 1 END) AS ups,
                count(CASE WHEN pv.vote='down' THEN 1 END) AS downs,
                count(commentId) as commentCount,
                i.imageId,i.link,i.width,i.height,
                u.userId, u.username
                FROM posts
                LEFT JOIN post_images USING(postId)
                LEFT JOIN images as i ON i.imageId = posts.cover
                LEFT JOIN post_votes as pv USING(postId)
                LEFT JOIN comments as c USING(postId)
                LEFT JOIN users as u on u.userId = posts.author
                GROUP BY postId
                order by score DESC limit $limit offset $skip";
        $posts = $this->db->query($sql)->result();
        $this->handlePosts($posts);
        return $posts;
    }

    private function handlePosts($posts)
    {
        foreach ($posts as $post) {
            $cover = $this->extractFields($post, array(KEY_IMAGE_ID, KEY_LINK, KEY_WIDTH, KEY_HEIGHT));
            $post->cover = $cover;
            $user = $this->extractFields($post, array(KEY_USER_ID, KEY_USERNAME));
            $post->author = $user;
            $post->points = $post->ups - $post->downs;
        }
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
        $sql = "SELECT created,score, count(CASE WHEN vote='up' THEN 1 END) AS ups,
                count(CASE WHEN vote='down' THEN 1 END) AS downs
                FROM posts LEFT JOIN post_votes USING(postId)
                WHERE postId=? GROUP BY postId";
        $values = array($postId);
        $post = $this->db->query($sql, $values)->row();
        $date = new DateTime($post->created);
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
