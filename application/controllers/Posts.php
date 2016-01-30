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
        $this->succeed($this->postDao->getPost($postId));
    }
}
