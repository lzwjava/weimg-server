<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 15/11/30
 * Time: 下午2:13
 */
class UserDao extends BaseDao
{
    private function checkIfUserUsed($field, $value)
    {
        $sql = "SELECT * FROM users WHERE $field =?";
        $array[] = $value;
        return $this->db->query($sql, $array)->num_rows() > 0;
    }

    function checkIfUsernameUsed($username)
    {
        return $this->checkIfUserUsed(KEY_USERNAME, $username);
    }

    function checkIfMobilePhoneNumberUsed($mobilePhoneNumber)
    {
        return $this->checkIfUserUsed(KEY_MOBILE_PHONE_NUMBER, $mobilePhoneNumber);
    }

    function insertUser($username, $mobilePhoneNumber, $avatarUrl, $password)
    {

        $data = array(
            KEY_USER_ID => $this->genId(),
            KEY_USERNAME => $username,
            KEY_PASSWORD => sha1($password),
            KEY_MOBILE_PHONE_NUMBER => $mobilePhoneNumber,
            KEY_AVATAR_URL => $avatarUrl,
            KEY_SESSION_TOKEN => $this->genSessionToken()
        );
        $this->db->trans_start();
        $this->db->insert(TABLE_USERS, $data);
        $this->db->trans_complete();
    }

    private function genId()
    {
        return getToken(16);
    }

    private function genSessionToken()
    {
        return getToken(32);
    }

    function checkLogin($mobilePhoneNumber, $password)
    {
        $sql = "SELECT * FROM users WHERE mobilePhoneNumber=? AND password=?";
        $array[] = $mobilePhoneNumber;
        $array[] = sha1($password);
        return $this->db->query($sql, $array)->num_rows() == 1;
    }

    private function findUser($field, $value, $cleanFields = true)
    {
        $user = $this->findActualUser($field, $value);
        if ($user) {
            if ($cleanFields) {
                $this->cleanUserFieldsForAll($user);
            }
        }
        return $user;
    }

    private function getPublicFields()
    {
        return $this->mergeFields(array(KEY_USER_ID, KEY_AVATAR_URL, KEY_USERNAME, KEY_TYPE));
    }

    private function getSessionUserFields()
    {
        return $this->mergeFields(array(
            KEY_USER_ID,
            KEY_AVATAR_URL,
            KEY_USERNAME,
            KEY_TYPE,
            KEY_MOBILE_PHONE_NUMBER,
            KEY_SESSION_TOKEN,
            KEY_CREATED,
            KEY_UPDATED,
            KEY_INTRODUCTION,
            KEY_COMPANY,
            KEY_JOB_TITLE,
            KEY_GITHUB_USERNAME
        ));
    }

    function findPublicUser($field, $value)
    {
        $fields = $this->getPublicFields();
        return $this->getOneFromTable(TABLE_USERS, $field, $value, $fields);
    }

    function findPublicUserById($id)
    {
        return $this->findPublicUser(KEY_USER_ID, $id);
    }

    private function findActualUser($field, $value)
    {
        $user = $this->getOneFromTable(TABLE_USERS, $field, $value);
        return $user;
    }

    function findUserBySessionToken($sessionToken)
    {
        return $this->findUser(KEY_SESSION_TOKEN, $sessionToken);
    }

    function findUserById($id)
    {
        return $this->findUser(KEY_USER_ID, $id);
    }

    private function updateSessionToken($user)
    {
        $token = $this->genSessionToken();
        $result = $this->updateUser($user, array(
            KEY_SESSION_TOKEN => $token,
            KEY_SESSION_TOKEN_CREATED => dateWithMs()
        ));
        if ($result) {
            $user->sessionToken = $token;
        }
    }

    function updateSessionTokenIfNeeded($mobilePhoneNumber)
    {
        $user = $this->findUser(KEY_MOBILE_PHONE_NUMBER, $mobilePhoneNumber, false);
        $created = strtotime($user->sessionTokenCreated);
        $now = dateWithMs();
        $nowMillis = strtotime($now);
        $duration = $nowMillis - $created;
        if ($user->sessionToken == null || $user->sessionTokenCreated == null
            || $duration > 60 * 60 * 24 * 30
        ) {
            $this->updateSessionToken($user);
        }
        $this->cleanUserFieldsForAll($user);
        return $user;
    }

    function updateUser($user, $data)
    {
        $tableName = TABLE_USERS;
        $this->db->where(KEY_USER_ID, $user->userId);
        $result = $this->db->update($tableName, $data);
        if ($result) {
            return $this->findUser(KEY_USER_ID, $user->userId);
        }
    }

    private function cleanUserFieldsForAll($user)
    {
        if ($user) {
            unset($user->sessionTokenCreated);
            unset($user->password);
        }
    }

    private function cleanUserFieldsForPrivacy($user)
    {
        if ($user) {
            unset($user->sessionToken);
            unset($user->mobilePhoneNumber);
            unset($user->created);
            unset($user->type);
        }
    }

}
