<?php
namespace http;

class CurlResponse{
    private $httpCode;
    private $content;

    public function __construct($httpCode,$content){
        $this->setCode($httpCode);
        $this->setContent($content);
    }

    public function setCode($httpCode){
        $this->httpCode = $httpCode;
    }

    public function setContent($content){
        $this->content = $content;
    }

    public function getContent(){
        return $this->content;
    }

    public function getCode(){
        return $this->httpCode;
    }
}