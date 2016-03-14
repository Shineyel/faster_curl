<?php
namespace worker;
use http\CurlRequest;

class BaseWorker extends \Thread{
    protected $autoload;

    public function __construct(CurlRequest $request){
        $this->initAutoLoad();
    }

    public function run(){
        $this->registerAutoLoad();
    }

    //before thread. get current thread's context(auto_load)
    private function initAutoLoad(){
        $this->autoload = spl_autoload_functions();
    }

    //depend on the initAutoLoad function. reRegister AutoLoad function.
    private function registerAutoLoad(){
        if(!empty($this->autoload)) {
            //注意，所有数组都被转换成了Volatile
            foreach ((array)$this->autoload as $load) {
                spl_autoload_register($load);
            }
        }else{
            spl_autoload_register('auto_load');
        }
    }
}