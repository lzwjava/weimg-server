<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 16/1/29
 * Time: 下午4:59
 */
class ImageDao extends BaseDao
{

    function publicFields()
    {
        return $this->mergeFields(array(KEY_IMAGE_ID,
            KEY_LINK, KEY_WIDTH, KEY_HEIGHT, KEY_AUTHOR, KEY_DESCRIPTION, KEY_CREATED),
            TABLE_IMAGES);
    }

    private function genImageId()
    {
        return getToken(6);
    }

    function addImage($imageId, $link, $author, $description, $width, $height)
    {
        $data = array(
            KEY_IMAGE_ID => $imageId,
            KEY_LINK => $link,
            KEY_WIDTH => $width,
            KEY_HEIGHT => $height,
            KEY_AUTHOR => $author,
            KEY_DESCRIPTION => $description,
        );
        $this->db->insert(TABLE_IMAGES, $data);
    }

    function getImage($imageId)
    {
        return $this->getOneFromTable(TABLE_IMAGES, KEY_IMAGE_ID, $imageId);
    }
}
