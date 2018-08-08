<?php
/**
 * Created by PhpStorm.
 * User: tsghe
 * Date: 2018/8/8
 * Time: 22:56
 */

namespace UsingTrait\Common\Controller;

use Phalcon\Filter;

trait ExtendController
{
    /**
     * @param null $field
     * @param null $filterType
     * @param null $defaultValue
     * @return array|mixed|null
     */
    public function getPost($field = null, $filterType = null, $defaultValue = null){
        $value = $this->request->getPost($field, $filterType, $defaultValue);
        if(!empty($value)){
            return $value;
        }
        $value = $this->request->getJsonRawBody(true);
        if(empty($value)){
            return [];
        }
        if(empty($field)){
            return $value;
        }
        return isset($value[$field]) ? (new Filter())->sanitize($value[$field], $filterType) : $defaultValue;
    }

    public function responseOk($data=[], $code = 200, $httpCode = 200,$status = 'ok'){
        return $this->response
            ->setStatusCode($httpCode)
            ->setHeader('Access-Control-Allow-Origin','*')
            ->setJsonContent([
                'ok'=>1,
                'status'=>$status,
                'code'=>$code,
                'data'=>$data
            ]);
    }

    public function responseError($message = null, $code = 500, $httpCode = 200,$status = 'error'){
        if(is_array($message)){
            $message = $message[0];
        }
        if($message instanceof Message){
            $message = $message->getMessage();
        }
        return $this->response
            ->setStatusCode($httpCode)
            ->setJsonContent([
                'ok'=>0,
                'status'=>$status,
                'code'=>$code,
                'message'=>$message
            ]);
    }
}