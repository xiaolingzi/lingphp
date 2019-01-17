<?php
class Process
{
    private $_workerArr = array();
    private $_configArr = array();
    private $_mainProcessStatus = true;
    private $_isFockSubProcesses = true;
    private $_subProcessStatus = true;
    public function start($configArr)
    {
        //未指定执行函数则直接退出
        if (empty($configArr) || !array_key_exists("workFunction", $configArr) || empty($configArr["workFunction"])) {
            echo "work function not setted!\n";
            return;
        }
        //初始化配置
        $this->initConfig($configArr);

        $func = $this->_configArr["workFunction"];
        $parameterArr = $this->_configArr["parameters"];
        // 如果不支持，则直接运行
        if (!function_exists('pcntl_fork')) {
            echo "pcntl_fork not supported, run directly!\n";
            call_user_func_array($func, $parameterArr);
            return;
        }

        //检查进程是否已经运行
        $processTitle = $this->_configArr["processTitle"];
        if ($this->checkProcess($processTitle)) {
            echo "Another process is runing, please kill first!\n";
            return;
        }

        // 如果不是以守护进程运行，就直接单进程运行
        if (!$this->_configArr["daemonize"]) {
            call_user_func_array($func, $parameterArr);
            return;
        }

        // 如果是非cli模式，就直接运行代码
        if (substr(php_sapi_name(), 0, 3) !== 'cli') {
            call_user_func_array($func, $parameterArr);
            return;
        }

        $workerNumber = intval($this->_configArr["workerNumber"]);
        if ($workerNumber <= 0) {
            //如果设置的进程数量小于等于0，那么直接默认开启一个守护进程进行处理
            $this->_configArr["workerNumber"] = 1;
            $this->fockSubProcesses();
        } else {
            //先开启一个主进程，然后再开启子进程，为了可以通过主进程管理子进程
            $this->fockMainProcess();
        }
        exit(0);
    }

    private function fockSubProcesses()
    {
        $this->log("fock start");

        $this->_workerArr = array();
        $func = $this->_configArr["workFunction"];
        $workerNumber = $this->_configArr["workerNumber"];
        $loopTimespan = intval($this->_configArr["loopTimespan"]);
        $parameterArr = $this->_configArr["parameters"];

        $this->_subProcessStatus = true;
        $this->log($workerNumber . " fock start " . $loopTimespan);
        for ($i = 0; $i < $workerNumber; $i++) {
            $pid = pcntl_fork();
            if ($pid == 0) {
                // 建立新session以脱离终端
                $sid = posix_setsid();
                if ($sid < 0) {
                    $this->log("set sid fail,exit!");
                    exit();
                }

                declare (ticks = 1);
                pcntl_signal(SIGUSR2, array(__CLASS__, "subSignalHandler"));

                // 子进程
                $subPID = getmypid();

                if ($loopTimespan <= 0) {
                    call_user_func_array($func, $parameterArr);
                    //子进程执行完之后要执行exit让子进程退出
                    exit(0);
                } else {
                    while ($this->_subProcessStatus) {
                        call_user_func_array($func, $parameterArr);
                        pcntl_signal_dispatch();
                        sleep($loopTimespan);
                    }
                    //子进程执行完之后要执行exit让子进程退出
                    exit(0);
                }
            } else if ($pid == -1) {
                throw new Exception('fork process fail!');
            } else if ($pid > 0) {
                //父进程
                $this->_workerArr["$pid"] = true;
            }
        }
    }

    private function fockMainProcess()
    {
        $workerNumber = $this->_configArr["workerNumber"];
        $processTitle = $this->_configArr["processTitle"];

        $this->log("main process");
        $pid = pcntl_fork();
        if ($pid == 0) {
            // 建立一个有别于终端的新session以脱离终端
            $sid = posix_setsid();
            if ($sid < 0) {
                echo "set sid fail,exit!\n";
                exit();
            }
            if (!empty($processTitle)) {
                cli_set_process_title($processTitle);
            }

            declare (ticks = 1);
            pcntl_signal(SIGUSR1, array(__CLASS__, "signalHandler"));
            pcntl_signal(SIGUSR2, array(__CLASS__, "signalHandler"));
            pcntl_signal(SIGTERM, array(__CLASS__, "signalHandler"));
            //不关心子进程退出，注册到内核让内核进行回收
            pcntl_signal(SIGCHLD, SIG_IGN);

            // 关闭打开的文件描述符
            fclose(STDIN);
            fclose(STDOUT);
            fclose(STDERR);

            $STDIN = fopen('/dev/null', 'r');
            $STDOUT = fopen('/dev/null', 'a');
            $STDERR = fopen('/dev/null', 'a');

            while ($this->_mainProcessStatus) {
                if ($this->_isFockSubProcesses) {
                    // 子进程
                    $this->fockSubProcesses();
                    $this->_isFockSubProcesses = false;
                }
                pcntl_signal_dispatch();
                sleep(1);
            }
        } else if ($pid == -1) {
            throw new Exception('fork process fail!');
        } else if ($pid > 0) {

        }
    }

    private function initConfig($configArr)
    {
        //进程数量，默认1
        if (!array_key_exists("workerNumber", $configArr)) {
            $configArr["workerNumber"] = 1;
        }
        //是否守护运行，默认否
        if (!array_key_exists("daemonize", $configArr)) {
            $configArr["daemonize"] = false;
        }
        //间隔执行时间，默认1秒。设置之后按时间间隔循环执行任务
        if (!array_key_exists("loopTimespan", $configArr)) {
            $configArr["loopTimespan"] = 1;
        }
        //进程标题，默认不设置
        if (!array_key_exists("processTitle", $configArr)) {
            $configArr["processTitle"] = "";
        }
        //执行方法的参数，默认为空
        if (!array_key_exists("parameters", $configArr)) {
            $configArr["parameters"] = array();
        }

        $this->_configArr = $configArr;
        return $configArr;
    }

    public function signalHandler($signal)
    {
        switch ($signal) {
            case SIGUSR1:
                $this->log("SIGUSR1 in");

                //自定义信号
                $this->killAllSubProcesses(true);

                //通知主进程重启所有子进程
                $this->_isFockSubProcesses = true;
                //直接在这里调用该方法fork新的子进程会有问题，比如会打断现有子进程的信号接收
                //$this->fockSubProcesses();

                $this->log("SIGUSR1 out");
                break;
            case SIGUSR2:
                $this->log("SIGUSR2 in");

                //自定义信号
                $this->killAllSubProcesses(false);

                //通知主进程重启所有子进程
                $this->_isFockSubProcesses = true;
                //直接在这里调用该方法fork新的子进程会有问题，比如会打断现有子进程的信号接收
                //$this->fockSubProcesses();

                $this->log("SIGUSR2 out");
                break;
            case SIGTERM:
                $this->log("SIGTERM in");

                $this->_mainProcessStatus = false;
                //父进程退出前退出所有子进程
                $this->killAllSubProcesses(true);

                $this->log("SIGTERM out");
                //退出父进程
                exit(0);
                break;
            default:
                break;
        }
    }

    public function subSignalHandler($signal)
    {
        switch ($signal) {
            case SIGUSR2:
                $this->log("sub SIGUSR2 in");

                //通知子进程结束
                $this->_subProcessStatus = false;

                $this->log("sub SIGUSR2 out");
                break;
            default:
                break;
        }
    }

    private function killAllSubProcesses($isKill = true)
    {
        $this->log(json_encode($this->_workerArr));
        if (!empty($this->_workerArr)) {
            if ($isKill) {
                foreach ($this->_workerArr as $pid => $value) {
                    $this->_workerArr["$pid"] = false;
                    $result = posix_kill($pid, SIGKILL);
                    //如果正常终止，需要调用pcntl_waitpid等待子进程结束回收占用资源，防止僵尸进程出现
                    //                     $result = posix_kill($pid, SIGTERM);
                    //                     $status=-1;
                    //                     pcntl_waitpid($pid, $status);
                    //                     if(pcntl_wifexited($status))
                    //                     {
                    //                         unset($this->_workerArr["$pid"]);
                    //                     }
                    //                     $this->log("$pid"."--".strval(pcntl_wifexited($status)));
                }
            } else {
                foreach ($this->_workerArr as $pid => $value) {
                    //通知子进程结束
                    $result = posix_kill($pid, SIGUSR2);
                }

            }
        }
    }

    private function checkProcess($processTitle)
    {
        if (empty($processTitle)) {
            global $command;
            $processTitle = ROOT_PATH . "/App.php -i $command";
        }
        $cmd = "ps axu|grep \"$processTitle\"|grep -v \"grep\"|wc -l";
        $result = shell_exec("$cmd");
        $result = trim($result, "\r\n");

        //为0则没有任何进程，考虑本进程就已经为1，只有为2才有另外的进程
        if ($result === "1") {
            return false;
        }
        return true;
    }

    private function checkSubProcess($pid)
    {
        $cmd = "pstree -p $pid | wc -l";
        $result = shell_exec("$cmd");
        $result = trim($result, "\r\n");
        if ($result > 0) {
            return true;
        }
        return false;
    }

    private function log($txt)
    {
        $exceptionMessage = $txt . "\n";

        $filePath = __DIR__ . "/logs/process";
        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $fileName = $filePath . "/" . date("Ymd", time()) . ".txt";
        $fp = fopen($fileName, "a");
        fwrite($fp, $exceptionMessage);
        fclose($fp);
    }

}
