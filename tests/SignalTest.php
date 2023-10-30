<?php

use PHPUnit\Framework\TestCase;

/**
 * 
 */
class SignalTest extends TestCase{
  private $logfile= __DIR__ .'/test.log';

  public function setup():void{
    $this->unlink($this->logfile);
  }

  private function unlink($filename){
    if(file_exists($filename) ){ 
      unlink($filename) ;
    }
  }


  /**
   * run script having a infinite loop
   * and kill using SIGHUP
   */
  public function testSignalOk(){
    $pid = $this->runscript('testscript');
    sleep(1);
    $result = posix_kill($pid, SIGTERM);
    $this->assertTrue($result);
    $this->assertMatchesRegularExpression('/exited by signal:/m', file_get_contents($this->logfile));
  }

  public function testCustomSignalHandlerOk(){
    $pid = $this->runscript('testscript_set');
    sleep(1);
    $result = posix_kill($pid, SIGTERM);
    $this->assertTrue($result);
    sleep(1);
    $this->assertMatchesRegularExpression('/exited by signal:/m', file_get_contents($this->logfile));
  }

  public function testCustomSignalHandlerOtherSignal(){
    $pid = $this->runscript('testscript_set');
    sleep(1);
    $result = posix_kill($pid, SIGQUIT);
    $this->assertTrue($result);
    $this->assertDoesNotMatchRegularExpression('/exited by signal:/m', file_get_contents($this->logfile));
  }

  private function runscript($script){
    $pid = pcntl_fork();
    if($pid == 0){
      pcntl_exec('/bin/env', ['php',__DIR__."/$script.php"]);
      exit(0);
    }

    return $pid ;
  }

  public function tearDown():void{
    $this->unlink($this->logfile);
  }
}