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

    function getPost($postId)
    {
        return $this->getOneFromTable(TABLE_POSTS, KEY_POST_ID, $postId);
    }

    function getPostList($skip, $limit)
    {
        $sql = "SELECT * FROM posts LEFT JOIN post_images USING(postId)
                LEFT JOIN images USING(imageId) GROUP BY postId
                order by score DESC limit $limit offset $skip";
        return $this->db->query($sql)->result();
    }
}
