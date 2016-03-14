<?php
/**
 * Created by PhpStorm.
 * User: Shine
 * Date: 16/3/11
 * Time: 下午5:11
 */
error_reporting(E_ALL);
for ($i=0; $i < 10; $i++) {
    $urls_array[] = array("name" => "baidu", "url" => "http://www.baidu.com/s?wd=".mt_rand(10000,20000));
}

$t = microtime(true);
foreach ($urls_array as $key => $value) {
    $result_new[$key] = model_http_curl_get($value["url"]);
}
$e = microtime(true);
echo "For循环(curl)：".($e-$t)."\n";

$t = microtime(true);
//$result = multiple_threads_request($urls_array);
$result = model_multi_http_curl_get($urls_array);
$e = microtime(true);
echo "Multi请求(multi_curl)：".($e-$t)."\n";

$t = microtime(true);
$result = model_thread_result_get($urls_array);
$e = microtime(true);
echo "pthreads:多线程+curl：".($e-$t)."\n";

$t = microtime(true);
$result = model_thread_multi_result_get($urls_array);
$e = microtime(true);
echo "pthreads:多线程+multi：".($e-$t)."\n";

//同步非阻塞curl
class test_thread_run extends Thread {
    public $url;
    public $data;

    public function __construct($url) {
        $this->url = $url;
    }

    public function run() {
        $this->data = model_http_curl_get($this->url);
        return true;
    }
}

function model_thread_result_get($urls_array) {
    foreach ($urls_array as $key => $value) {
        $thread_array[$key] = new test_thread_run($value["url"]);
        $thread_array[$key]->start();
    }

    foreach ($thread_array as $thread_array_key => $thread_array_value) {
        while($thread_array[$thread_array_key]->isRunning()) {
            usleep(5);
        }
        if($thread_array[$thread_array_key]->join()) {
            $variable_data[$thread_array_key] = $thread_array[$thread_array_key]->data;
        }
    }
    return $variable_data;
}

//异步非阻塞curl
class test_thread_multi_run extends Thread {
    public $urls;
    public $data;

    public function __construct($urls) {
        $this->urls = $urls;
    }

    public function run() {
        $data = model_multi_http_curl_get($this->urls);
        $this->data = (array)$data;
        return true;
    }
}
function model_thread_multi_result_get($urls_array){
    $thread = new test_thread_multi_run($urls_array);
    $thread->start();
    while ($thread->isRunning()) {
        usleep(5);
    }
    if ($thread->join()) {
        $result = $thread->data;
    }

    return $result;
}

function model_http_curl_get($url,$userAgent="") {
    $userAgent = $userAgent ? $userAgent : 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2)';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function model_multi_http_curl_get($urls_array){
    $responses = array();
    $curl_arr = array();

    $curlMultiHandle = curl_multi_init();
    foreach($urls_array as $key => $url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url['url']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2)');

        curl_multi_add_handle($curlMultiHandle, $curl);
        $curl_arr[$key] = $curl;
    }

    $active = null;
    do {
        do {
            //curl_multi_exec的返回值判断是否还有数据
            //当有数据的时候就不停调用curl_multi_exec
            $status = curl_multi_exec($curlMultiHandle, $active);
        } while ($status == CURLM_CALL_MULTI_PERFORM);
        if ($status != CURLM_OK) break;

        if($active == false) {
            //$active要等全部url数据接受完毕才变成false
            foreach ($urls_array as $key => $url) {
                $responses[$key]['content'] = curl_multi_getcontent($curl_arr[$key]);
                $responses[$key]['httpcode'] = curl_getinfo($curl_arr[$key], CURLINFO_HTTP_CODE);
                curl_multi_remove_handle($curlMultiHandle, $curl_arr[$key]);
                curl_close($curl_arr[$key]);
            }
        }else{
            //暂时没有数据就进入select阶段
            //新数据一来就可以被唤醒继续执行
            curl_multi_select($curlMultiHandle,0.05);
        }
    } while ($active);
    curl_multi_close($curlMultiHandle);
    return $responses;
}

