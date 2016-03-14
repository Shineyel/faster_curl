<?php
namespace curl;
use http\CurlResponse;

class SingleCurl extends CurlBase{
    protected $curlHandle;

    public function open() {
        $this->curlHandle = $this->initSingleCurl();
    }

    public function send($request) {
        if (is_array($request) || empty($request) || empty($this->curlHandle)) {
            return NULL;
        }
        $this->setUrl($this->curlHandle,$request);
        $this->setopt($this->curlHandle,$request);
    }

    public function exec() {
        if(empty($this->curlHandle)){
            return false;
        }
        $content = curl_exec($this->curlHandle);
        $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        //若返回的是json且希望转换，记得强制转换为array
        //$content = (array)json_decode($content, TRUE);
        $response = new CurlResponse($httpCode,$content);

        return $response;
    }

    public function close() {
        if(!empty($this->curlHandle)) {
            curl_close($this->curlHandle);
            $this->curlHandle = NULL;
        }
    }
}