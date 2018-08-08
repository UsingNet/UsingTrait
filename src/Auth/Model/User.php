<?php
/**
 * Created by PhpStorm.
 * User: tsghe
 * Date: 2018/8/8
 * Time: 21:20
 */

namespace UsingTrait\Auth\Model;


trait User
{
    public $id;
    public $username;
    protected $password;

    public function setPassword($password){
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($password){
        return password_verify($password, $this->password);
    }
}