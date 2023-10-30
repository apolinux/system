<?php

namespace Apolinux;

/**
 * Process Signal Management
 * 
 * Define handler for system signals. Signals handler must set with 
 * initHandler or setHandler first.
 * 
 * Then in code, perharps inside a loop, getExitStatus is called
 * to verify if signal is called.
 * 
 * This class is often used in loops when some tasks are slow because are using I/O or database calls.
 * In order to avoid stop suddenly the process running, after the slow code is running, 
 * the getExitStatus method is called and check if some stop signal has activated, 
 * and the program can be stopped normally.
 */
class Signal {

    /**
     * exit status code
     * @var int
     */
    private static $exit_status = false;

    /**
     * last signal called
     * @var int
     */
    public static $last_signal ;

    /**
     * get exit status
     * 
     * using in some part of code to define status of $exit_status 
     * when signal is activated
     * @return int
     */
    public static function getExitStatus(){
        pcntl_signal_dispatch();
        return self::$exit_status;
    }

    /**
     * set exit status of process
     * 
     * used to communicate other parts of code and define status
     * 
     * @param int
     */
    public static function setExitStatus($status){
        self::$exit_status = $status ;
    }

    /**
     * Setup signal handlers
     * 
     * define default handler for common signals
     * - SIGTERM : send for normal program termination
     * - SIGHUP :  send for program termination when terminal is disconnected
     * - SIGNIT : Send for program interruption like CTRL-C
     * @see https://www.gnu.org/software/libc/manual/html_node/Termination-Signals.html
     */
    public static function initHandler(){
        pcntl_signal(SIGTERM, [Signal::class, 'sigHandler']);
        pcntl_signal(SIGHUP,  [Signal::class, 'sigHandler']);
        pcntl_signal(SIGINT,  [Signal::class, 'sigHandler']);
    }

    /**
     * Assign handler for signals
     * 
     * Signals can be SIGTERM, SIGINT, SIGQUIT,etc
     * 
     * @param int       $signal
     * @param callable  $handler
     * @param bool      $restart_syscalls
     * @return bool
     */
    public static function setHandler(int $signal, callable $handler, $restart_syscalls=true){
        return pcntl_signal($signal, $handler, $restart_syscalls);
    }

    /**
     * handler to be called when signals are activated
     * by default call static function setExitStatus()
     * 
     * @param int $signo signal number
     */
    public static function sigHandler($signo)
    {
        Signal::$last_signal = $signo ;
        switch ($signo) {
            case SIGTERM:
            case SIGHUP :
            case SIGINT:
                // handle shutdown tasks
                Signal::setExitStatus(true);

                break;
            default:
                 // handle all other signals
        }
    }
}
