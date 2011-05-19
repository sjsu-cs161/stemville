<?php
require_once 'settings.php';
// SCRIPT FOUND ON  PHP.NET WEBSITE AND SLIGHTLY MODIFIED

/* An easy way to keep in track of external processes.
 * Ever wanted to execute a process in php, but you still wanted to have somewhat controll of the process ? Well.. This is a way of doing it.
 * @compability: Linux only. (Windows does not work).
 * @author: Peec
 */
class Process{
    private $pid;
    private $command;

    public function __construct($cl=false){
        if ($cl != false){
            $this->command = $cl;
            $this->runCom();
        }
    }
    // Executes command and stores PID when called
    private function runCom(){
	
	$STEM_COMMAND = 'cd ' . STEM_ROOT_PATH . ' && export DISPLAY=:0 && nohup '.$this->command.' > ' . BASE_PATH . '/stemweblog 2>&1 & echo $!';
        exec($STEM_COMMAND ,$op);
        $this->pid = (int)$op[0];
    }

    // Set PID of Process instance
    public function setPid($pid){
        $this->pid = $pid;
    }
    // PID getter
    public function getPid(){
        return $this->pid;
    }
    // Get status -- running or not?
    public function status(){
        $command = 'ps -p '.$this->pid;        
        exec($command,$op);
        if (!isset($op[1]))return false;
        else return true;
    }
    // Execute command (start process)
    public function start(){
        if ($this->command != '')$this->runCom();
        else return true;
    }
    // Stop a process
    public function stop(){
        $command = 'kill '.$this->pid;
        exec($command);
        if ($this->status() == false)return true;
        else return false;
    }
}
?>
