# ![xiaolingzi](https://raw.githubusercontent.com/xiaolingzi/LingApp-PHP-Application/master/logo.gif) LingApp php脚本简易框架  
更多分享请访问 [https://www.xxling.com](https://www.xxling.com)  
## 说明  
  对于web程序，有太多较为成熟的框架可以使用，我们直接拿来用即可。而php除了写web代码之后，还经常用来写一些脚本任务，如服务器部署的一些计划任务程序或者临时使用的处理程序。之前都是大家按自己的想法随便去写，这样会导致代码的凌乱不好管理，另一方面也不便于代码的复用，故开始构建一个简单的框架。  
框架需要解决的主要问题如下：  
  1. 类自动加载，命名空间的引入，这是框架的基本。这样可以避免文件的到处引用。  
  2. 项目集中管理。各个程序归总起来管理。  
  3. 统一的调用方式。每个项目有统一的执行入口和参数选项。  
  4. 代码复用。引入简单的分层架构，将代码进行有组织管理。  
  5. 配置区分开发环境和线上环境。开发环境的很多配置比如文件路径都和正式服务器的不一样，如果不做分离，不小心就会把开发环境的配置覆盖掉正式的配置导致出错。
  6. 统一的文件命名规范。
  7. Linux下多进程和守护进程的支持。
  
详细说明请查看[https://www.xxling.com/blog/article/3104.aspx](https://www.xxling.com/blog/article/3104.aspx)
