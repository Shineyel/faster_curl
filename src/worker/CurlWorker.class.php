<?php
namespace worker;

use \http\CurlRequest;
use \curl\SingleCurl;
use \http\ResponseHandler;

class CurlWorker extends BaseWorker{
    private $request;
    private $response;

    public function __construct(CurlRequest $request){
        parent::__construct($request);
        $this->request = $request;
        $this->start();
    }

    public function run(){
        parent::run();

        //调用curl里面代码进行请求
        $singleCurl = SingleCurl::instance();
        $singleCurl->open();
        $singleCurl->send($this->request);
        $response = $singleCurl->exec();
        $singleCurl->close();

        //如果request中有handler则，进行处理
        if(isset($this->request->handler)
            && $this->request->handler instanceof ResponseHandler){
            $response = $this->request->handler->handleResponse($response);
        }

        $this->response = $response;
    }

    //获取线程执行结果
    public function getResult(){
        while($this->isRunning()){
            usleep(5);
        }
        if($this->join()){
            return $this->response;
        }
        return false;
    }
}