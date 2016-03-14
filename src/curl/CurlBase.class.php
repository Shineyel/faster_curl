<?php
namespace curl;
use http\CurlRequest;
abstract class CurlBase{
    const DefaultTimeOut = 2;  //默认接口超时时间
    const DefaultTimOutConn = 1; //默认连接时间

    protected $useragent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2)';

    //多线程，不适用单例模式
    public static function instance(){
        $class = get_called_class();
        return new $class;
    }

    public abstract function open();
    public abstract function send($request);
    public abstract function exec();
    public abstract function close();

    protected function getHeaders(){
        $headerArr = array();
        return $headerArr;
    }

    //初始化一个基本的handler
    protected function initSingleCurl(){
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($curlHandle, CURLOPT_USERAGENT, $this->useragent);
        //如果 CURLOPT_RETURNTRANSFER 选项被设置，函数执行成功时会返回执行的结果，失败时返回 FALSE。
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curlHandle, CURLOPT_HEADER, FALSE);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);

        // 设置referer
        if(isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REQUEST_URI'])) {
            $referer = $_SERVER['SERVER_NAME'] . str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
            curl_setopt($curlHandle,CURLOPT_REFERER,$referer);
        }
        return $curlHandle;
    }

    //设置Url
    protected function setUrl($curlHandle,CurlRequest $request){
        $params = http_build_query($request->params);
        $uri = $request->uri;
        $method = strtoupper($request->method);
        switch ($method) {
            case 'GET':
                curl_setopt($curlHandle, CURLOPT_HTTPGET, TRUE);
                $uri .= '?' . $params;
                break;
            case 'POST':
            default :
                curl_setopt($curlHandle, CURLOPT_POST, TRUE);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $params);
                break;
        }
        curl_setopt($curlHandle, CURLOPT_URL, $uri);
        return $curlHandle;
    }

    //给curl请求设置各种参数
    protected function setOpt($curlHandle,CurlRequest $request){
        $options = $request->opt;

        if (empty($options['timeout'])) {
            $options['timeout'] = self::DefaultTimeOut;
        }

        if (empty($options['connect_timeout'])) {
            $options['connect_timeout'] = self::DefaultTimOutConn;
        }

        foreach ($options as $type => $value) {
            switch ($type) {
                case 'timeout':
                    curl_setopt($curlHandle, CURLOPT_TIMEOUT, $value);
                    break;
                case 'connect_timeout':
                    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, $value);
                    break;
                case 'timeout_ms':
                    curl_setopt($curlHandle, CURLOPT_TIMEOUT_MS, $value);
                    break;
                case 'connect_timeout_ms':
                    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT_MS, $value);
                    break;
                case 'header':
                    $value = !empty($value) ? array_merge($this->getHeaders(), explode(';', $value)) : $this->getHeaders();
                    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $value);
                    break;
            }
        }
        return $curlHandle;
    }

}