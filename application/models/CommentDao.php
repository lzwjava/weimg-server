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

}