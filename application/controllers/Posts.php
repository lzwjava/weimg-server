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
        $imageIds = $this->post(KEY_IMAGE_IDS);
        $title = $this->post(KEY_TITLE);
        $topic = $this->post(KEY_TOPIC);
        $ok = $this->postDao->addPost($imageIds, $title, $user->userId, $topic);
        if ($ok) {
            $this->succeed();
        } else {
            $this->failure(ERROR_RUN_SQL_FAILED);
        }
    }
}
