<?php
/**
 * Created by PhpStorm.
 * User: tsghe
 * Date: 2018/8/8
 * Time: 21:35
 */

namespace UsingTrait\Auth\Controller;


use UsingTrait\Auth\Model\User;
use UsingTrait\Common\Controller\ExtendController;
use UsingTrait\Common\Exception\FilterFormatError;
use UsingTrait\Common\Utility\Query;

trait Login
{
    use ExtendController;

    abstract function getLoginModel();

    protected $loginIdentificationFields = ['username'];

    public function loginAction(){
        $filter = [];
        foreach($this->loginIdentificationFields as $key=>$field){
            if(is_numeric($key)){
                $filter[$field] = $this->getPost($field);
            }else{
                $filter[$key] = $this->getPost($field);
            }
        }
        try {
            $user = $this->getLoginModel()::findFirst(Query::filterToParameters($filter));
            if(!$user){
                return $this->responseError('Username or Password Error');
            }
            if(!$user instanceof User){
                return $this->responseError(sprintf('getLoginModel should return an instance of UsingTrait\Auth\Model\User but taken a(n) ', get_class($user)));
            }
            if(!$user->verifyPassword($this->getPost('password'))){
                return $this->responseError('Username or Password Error');
            }

            return $this->responseOk($user);
        } catch (FilterFormatError $e) {
            return $this->responseError($e->getMessage(), $e->getCode());
        }
    }
}