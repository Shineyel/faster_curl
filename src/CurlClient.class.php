<?php
use \http\CurlRequest;
use \worker\CurlWorker;

class CurlClient{
    private $workerPool = array();
    private $resultList = array();

    public function execute(CurlRequest $request,$index) {
        $this->workerPool[$index] = new CurlWorker($request);
        return true;
    }

    public function get($index){
        if(isset($this->workerPool[$index])
            && $this->workerPool[$index] instanceof CurlWorker){
            if(empty($this->resultList[$index])) {
                //获取结果
                $this->resultList[$index] = $this->workerPool[$index]->getResult();
                //注销worker线程处理器
                //(其实应该wait，等待notify，减少启用线程的资源耗费)
                unset($this->workerPool[$index]);
            }
            return $this->resultList[$index];
        }
        return false;
    }
}