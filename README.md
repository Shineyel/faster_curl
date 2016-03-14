#faster_curl
一个基于PHP的异步非阻塞的http crul模块。

###要求：
* 依赖[pthreads](https://github.com/krakjoe/pthreads)
* [(PHP多线程)Pthreads安装&使用](http://shineyel.github.io/blogs/pthreads.html)

###例子(tests目录)
* PthreadTest.php：一个测试pthreads的例子
* demo目录下为faster_curl的测试例子

###当前特性（v1.0）：
* 支持异步请求http

###待优化特性：
* 异步支持批量
* 线程池管理，减少线程创建销毁消耗

###UML类图:
[http://shineyel.github.io/blogs/faster_curl.html](http://shineyel.github.io/blogs/faster_curl.html)
![uml](http://shineyel.github.io/images/faster_curl_01.png)