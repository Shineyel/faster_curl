<?php
namespace http;

class CurlRequest{
    private $uri = '';
    private $method = 'POST';
    private $params = array();
    private $handler = null;
    private $opt = array();

    public function __construct($uri,$params,ResponseHandler $handler=null,$method='POST',$opt=array()){
        $this->setUri($uri);
        $this->setParams($params);
        $this->setHandler($handler);
        $this->setMethod($method);
        $this->setOpt($opt);
    }

    //setter
    public function setMethod($method){
        $this->method = $method;
    }
    public function setUri($uri){
        $this->uri = $uri;
    }
    public function setParams(array $params){
        $this->params = $params;
    }
    public function setHandler(ResponseHandler $handler=null){
        if(isset($handler)) {
            $this->handler = $handler;
        }
    }
    public function setOpt(array $opt){
        $this->opt = $opt;
    }
    //getter
    public function __get($key){
        return $this->$key;
    }


    public function __toString(){
        return $this->method . " " . $this->uri;
    }
}