<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 16/2/10
 * Time: 下午3:50
 */
class Comments extends BaseController
{
    public $commentDao;

    function __construct()
    {
        parent::__construct();
        $this->load->model('commentDao');
        $this->commentDao = new CommentDao();
    }

    function create_post($postId)
    {
        if ($this->checkIfParamsNotExist($this->post(), array(KEY_CONTENT))) {
            return;
        }
        $content = $this->post(KEY_CONTENT);
        $parentId = $this->post(KEY_PARENT_ID);
        $user = $this->checkAndGetSessionUser();
        if (!$user) {
            return;
        }
        $commentId = $this->commentDao->addComment($postId, $parentId, $content, $user->userId);
        $this->succeed(array(KEY_COMMENT_ID => $commentId));
    }

    function vote_get($commentId, $vote)
    {
        $user = $this->checkAndGetSessionUser();
        if (!$user) {
            return;
        }
        if ($this->checkIfNotInArray($vote, $this->voteArray())) {
            return;
        }
        $this->commentDao->voteComment($user->userId, $commentId, $vote);
        $this->succeed();
    }

}