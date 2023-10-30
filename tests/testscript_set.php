<?php

use Apolinux\Signal;

require_once __DIR__ .'/../vendor/autoload.php' ;

Signal::setHandler(SIGTERM,function($signal){
  Signal::$last_signal=$signal ;
  Signal::setExitStatus(true);
});

function write($text){
  file_put_contents(__DIR__ .'/test.log', $text. PHP_EOL, FILE_APPEND) ;
}

write('running loop');
$cont=0;
while($cont++<10){
  $status = Signal::getExitStatus();
  if($status){
    write('exited by signal: ' . Signal::$last_signal);
    break ;
  }
  sleep(1);
}