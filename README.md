#faster_curl
一个基于PHP的异步非阻塞的http crul模块。

###要求：
* 依赖pthreads：https://github.com/krakjoe/pthreads

###测试环境：
* mac os 10.11.1
* PHP 7.0.2

###当前特性（v1.0）：
* 支持异步请求http。

###待优化特性：
* 异步支持批量
* 线程池管理，减少线程创建销毁消耗。

###UML类图:
[http://shineyel.github.io/blogs/faster_curl.html](http://shineyel.github.io/blogs/faster_curl.html)
![uml](http://shineyel.github.io/images/faster_curl_01.png)
