# System

This library is createad to manage system operative functions. 

### Installation

```
composer require apolinux/system
```

### Signal library

Signal library allows to define handlers and call methods activated by system signals.


### Example

```

use Apolinux\Signal;

require_once __DIR__ .'/../vendor/autoload.php' ;

// define custom signal by default
Signal::setHandler(SIGUSR1,function($signal){
  Signal::$last_signal=$signal ;
  Signal::setExitStatus('OK');
});

posix_kill(posix_getpid(), SIGUSR1);

$status = Signal::getExitStatus();

// must show "OK" as status
echo "signal exit status: $status".PHP_EOL ;

```    
