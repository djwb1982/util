<?php
/**
 * ArrayTool 
 * 数组工具类
 */
namespace Agile\Utils;
class ArrayTool {
    /**
     * 将xml转换为数组
     * @param $xml  需要转化的xml
     * @return mixed
     */
    static function xml_to_array($xml)
    {
        $ob = simplexml_load_string($xml);
        $json = json_encode($ob);
        $array = json_decode($json, true);
        return $array;
    }

    /**
     * 将数组转化成xml
     * @param $data 需要转化的数组
     * @return string
     */
    static function data_to_xml($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        $xml = '';
        foreach ($data as $key => $val) {
            if (is_null($val)) {
                $xml .= "<$key/>\n";
            } else {
                if (!is_numeric($key)) {
                    $xml .= "<$key>";
                }
                $xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : $val;
                if (!is_numeric($key)) {
                    $xml .= "</$key>";
                }
            }
        }
        return $xml;
    }

    /**
     * 接收json数据并转化成数组
     * @return mixed
     */
    static function getJsonData()
    {
        $bean = file_get_contents('php://input');
        $result = json_decode($bean, true);
        return $result;
    }
}