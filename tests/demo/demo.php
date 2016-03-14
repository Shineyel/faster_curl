<?php

require __DIR__ . '/autoload.php';

$client = new \CurlClient();
for ($i=0; $i < 10; $i++) {
    $keys[] = 'baidu'.$i;
    $request = new \http\CurlRequest('www.ip.cn',array('wd'=>mt_rand(10000,20000)));
    $client->execute($request,$keys[$i]);
}

foreach($keys as $key) {
    $result[] = $client->get($key);
}

var_dump($result);
