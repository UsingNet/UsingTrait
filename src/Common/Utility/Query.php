<?php
/**
 * Created by PhpStorm.
 * User: tsghe
 * Date: 2018/8/8
 * Time: 21:42
 */

namespace UsingTrait\Common\Utility;


use UsingTrait\Common\Exception\FilterFormatError;

class Query
{
    /**
     * @param $filter
     * @param string $default
     * @return string
     * @throws FilterFormatError
     */
    static public function filterToParameters($filter, $default = '1 = 1'){
        if(count($filter) == 0){
            return $default;
        }else{
            return self::filterSegmentToParameters($filter);
        }
    }

    /**
     * @param $filter
     * @param string $upKey
     * @return string
     * @throws FilterFormatError
     */
    static private function filterSegmentToParameters($filter, $upKey = '$AND'){
        if(is_array($filter)){
            $fragments = [];
            foreach($filter as $key=>$value){
                if(substr($key, 0, 1) != '$'){
                    $fragments[] = self::filterSegmentToParameters($value, $key);
                }else{
                    switch (strtoupper($key)){
                        case '$AND':case '$OR':
                            $fragments[] = self::filterSegmentToParameters($value, $key);
                            break;
                        case '$GT':case '$GTE':case '$LT':case '$LTE':case '$IN':case '$NIN':case '$EQ':case 'NE':
                            $fragments[] = self::filterExpressionToParameter($upKey, $key, $value);
                            break;
                        case '$NOT':
                            $fragments[] = sprintf('NOT (%s)', self::filterSegmentToParameters($value, '$AND'));
                            break;
                        default:
                            throw new FilterFormatError();
                    }
                }
            }
            foreach($fragments as &$value){
                $value = sprintf('(%s)', $value);
            }
            return implode(' '.substr($upKey, 1).' ', $fragments);
        }else{
            return self::filterExpressionToParameter($upKey, '$EQ', $filter);
        }
    }

    static private $valueOps = [
        '$GT'=>'>',
        '$GTE'=>'>=',
        '$LT'=>'<',
        '$LTE'=>'<=',
        '$NEQ'=>'<>',
        '$EQ'=>'=',
        '$IN'=>'IN',
        '$NIN'=>'NOT IN',
    ];

    /**
     * @param $upKey
     * @param $opKey
     * @param $value
     * @return string
     * @throws FilterFormatError
     */
    static private function filterExpressionToParameter($upKey, $opKey, $value){
        if(!isset(self::$valueOps[$opKey])){
            throw new FilterFormatError();
        }
        $valueOp = self::$valueOps[$opKey];
        if(is_array($value)){
            $valueArray = [];
            foreach($value as $v){
                $valueArray[] = self::escapeToParam($v);
            }
            return sprintf("%s %s (%s)", self::escape($upKey), $valueOp, implode(', ', $valueArray));
        }else{
            return sprintf("%s %s %s", self::escape($upKey), $valueOp, self::escapeToParam($value));
        }
    }

    /**
     * @param $value
     * @return string
     * @throws FilterFormatError
     */
    static private function escapeToParam($value){
        if(is_numeric($value)){
            return strval($value);
        }else if(is_string($value)) {
            return sprintf("'%s'", self::escape($value));
        }else{
            throw new FilterFormatError();
        }
    }

    static public function escape($string){
        return sqlite_escape_string($string);
    }
}