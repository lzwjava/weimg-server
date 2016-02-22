<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 16/1/29
 * Time: 下午10:02
 */
class Posts extends BaseController
{
    public $postDao;

    function __construct()
    {
        parent::__construct();
        $this->load->model('PostDao');
        $this->postDao = new PostDao();
        $this->load->helper('score');
    }

    function create_post()
    {
        if ($this->checkIfParamsNotExist($this->post(), array(KEY_IMAGE_IDS, KEY_TITLE))) {
            return;
        }
        $user = $this->checkAndGetSessionUser();
        if (!$user) {
            return;
        }
        $imageIdsStr = $this->post(KEY_IMAGE_IDS);
        $imageIds = json_decode($imageIdsStr);
        $title = $this->post(KEY_TITLE);
        $topic = $this->post(KEY_TOPIC);
        $postId = $this->postDao->addPost($imageIds, $title, $user->userId, $topic);
        if ($postId !== false) {
            $this->succeed(array(KEY_POST_ID => $postId));
        } else {
            $this->failure(ERROR_RUN_SQL_FAILED);
        }
    }

    function fetch_get($postId)
    {
        $user = $this->getSessionUser();
        $post = $this->postDao->getPost($user, $postId);
        $this->succeed($post);
    }

    function list_get()
    {
        $user = $this->getSessionUser();
        $skip = $this->skip();
        $limit = $this->limit();
        $sort = $this->sortValue(KEY_SCORE);
        $posts = $this->postDao->getPostList($user, $skip, $limit, $sort);
        $this->succeed($posts);
    }

    function vote_get($postId, $vote)
    {
        $user = $this->checkAndGetSessionUser();
        if (!$user) {
            return;
        }
        if ($this->checkIfNotInArray($vote, $this->voteArray())) {
            return;
        }
        $newVote = $this->postDao->votePost($user->userId, $postId, $vote);
        $this->succeed(array(KEY_VOTE => $newVote));
    }

}
