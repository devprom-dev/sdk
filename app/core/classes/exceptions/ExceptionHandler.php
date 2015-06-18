<?php
namespace core\classes;

/**
 * Перехват и обработка сообщений об ошибках
 *
 * trigger_error('Сгенерированная ошибка', E_USER_NOTICE);
 */

class ExceptionHandler
{
	private $listeners = array();
	
    /**
     * Регистрация метода который будет вызван в конце работы скрипта
     */
    public function __construct( array $listeners = array() )
    {
    	$this->listeners = $listeners;
    	
        register_shutdown_function(array($this, 'endError'));
        
        set_error_handler(array($this, 'captureError'), E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING & ~E_DEPRECATED );
        
        set_exception_handler(array($this, 'exceptionError'));
    }

    /**
     * Завершение выполнения скрипта
     *
     * @param null $code Код ошибки
     */
    public function _exit($code = null)
    {
    	if ( strpos($_SERVER['REQUEST_URI'], '/500', 0) !== false )
    	{
    		echo 'Unhandled exception:<br/>'; 
    		
    		print_r($_SESSION['error']);
    		
    		die();
    	}
    	
        header('HTTP/1.1 500 Internal Server Error', true, 500);
        
        exit(header('Location: /500'));
    }

    /**
     * debug_backtrace
     *
     * @return array
     */
    public function _debug_backtrace()
    {
        return debug_backtrace();
    }

    /**
     * error_get_last
     *
     * @return mixed
     */
    public function _error_get_last()
    {
        return error_get_last();
    }

    /**
     * Завершение выполнения
     *
     * @param array   $data Данные для логирования/Данные по ошибке
     * @param integer $code Код ошибки для exit
     */
    public function halt($data, $code = null)
    {
        if (!session_id()) {
            @session_start();
        }
        $_SESSION['error'] = array_merge(
            array(
                'id' => time()
            ),
            $data
        );

        array_walk_recursive($data, function (&$item, $key) 
        {
            if (is_object($item))
            {
                $item = '[object: ' . get_class($item) . ']';
            }
        });

        foreach( $this->listeners as $listener )
        {
        	$listener->handle( $data, $code );
        }
        
        $this->_exit($code);
    }

    /**
     * Получение строкового обозначения кода ошибки
     *
     * @param  int $errno Код ошибки
     *
     * @return string
     */
    public function getErrorTypeStr($errno)
    {
        switch ($errno){
            case E_ERROR:   // 1
                return 'E_ERROR';
            case E_WARNING: // 2
                return 'E_WARNING';
            case E_PARSE:  // 4
                return 'E_PARSE';
            case E_NOTICE: // 8
                return 'E_NOTICE';
            case E_CORE_ERROR:    // 16
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:  // 32
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64
                return 'E_COMPILE_ERROR';
            //case E_CORE_WARNING: // 128 //
            //    return 'E_COMPILE_WARNING'; break;
            case E_USER_ERROR:   // 256
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512
                return 'E_USER_WARNING';
            case E_USER_NOTICE:  // 1024
                return 'E_USER_NOTICE';
            case E_STRICT:       // 2048
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:        // 8192
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:   // 16384
                return 'E_USER_DEPRECATED';

        }

        return 'NO_CODE';
    }
    
    /**
     * Функция, как обработчик ошибок
     *
     * @param integer      $errno      Первый аргумента errno содержит уровень ошибки в виде целого числа
     * @param string       $errstr     Второй аргумент errstr содержит сообщение об ошибке в виде строки.
     * @param string|null  $errfile    Третий необязательный аргумент errfile содержит имя файла, в котором
     *                                 произошла ошибка, в виде строки.
     * @param integer|null $errline    Четвертый необязательный аргумент errline содержит номер строки, в
     *                                 которой произошла ошибка, в виде целого числа.
     * @param array|null   $errcontext Пятый необязательный аргумент errcontext содержит массив указателей
     *                                 на активную таблицу символов в точке, где произошла ошибка. Другими
     *                                 словами, errcontext будет содержать массив всех переменных, существующих
     *                                 в области видимости, где произошла ошибка. Пользовательский обработчик
     *                                 не должен изменять этот контекст.
     */
    public function captureError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if ( $errno == E_NOTICE ) return true;
        
        if ( $errno == E_STRICT ) return true;
        
        if ( $errno == E_WARNING ) return true;
        
        if ( $errno == E_DEPRECATED ) return true;
        
        if ( strpos($_SERVER['REQUEST_URI'], '/500', 0) !== false ) return true;

        $debug = $this->_debug_backtrace();
        
        unset($debug[0]);

        $data = array(
            'type'   => 'error',
            'error'  => array(
                'errno'   => $errno,
                'message' => $errstr,
                'errfile' => $errfile,
                'errline' => $errline,
            ),
            'debug'  => array_map(function ($value) {
                unset($value['object']);
                
                $value['args'] = array_map(function ($value) {
                    if (is_array($value)) {
                        $value = '[array]';
                    } else if (gettype($value) == 'object') {
                        $value = get_class($value);
                    }
                    return $value;
                }, is_array($value['args']) ? $value['args'] : array());
                
                return $value;
            }, is_array($debug) ? $debug : array()),
            'env'    => isset($_ENV)    ? $_ENV    : null,
            'post'   => isset($_POST)   ? $_POST   : null,
            'get'    => isset($_GET)    ? $_GET    : null,
            'cookie' => isset($_COOKIE) ? $_COOKIE : null,
            'server' => isset($_SERVER) ? $this->cleanData($_SERVER) : null,
            'files'  => isset($_FILES)  ? $_FILES  : null,
        );

        $this->halt($data, $errno);
    }

    /**
     * Функция, как обработчик не обработанных исключений
     *
     * @param \Exception $e
     */
    public function exceptionError($e)
    {
    	if ( is_a($e, 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException') ) return;
    	
        $debug = $e->getTrace();
        unset($debug[0]);

        $data = array(
            'type' => 'exception',
            'error' => array(
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ),
            'debug'  => array_map(function ($value) {
                unset($value['object']);

                $value['args'] = array_map(function ($value) {
                    if (is_array($value)) {
                        $value = '[array]';
                    } else if (gettype($value) == 'object') {
                        $value = get_class($value);
                    }
                    return $value;
                }, is_array($value['args']) ? $value['args'] : array());

                return $value;
            }, is_array($debug) ? $debug : array()),
            'env'    => isset($_ENV)    ? $_ENV    : null,
            'post'   => isset($_POST)   ? $_POST   : null,
            'get'    => isset($_GET)    ? $_GET    : null,
            'cookie' => isset($_COOKIE) ? $_COOKIE : null,
            'server' => isset($_SERVER) ? $this->cleanData($_SERVER) : null,
            'files'  => isset($_FILES)  ? $_FILES  : null,
        );

        $this->halt($data, $e->getCode());
    }

    /**
     * Функция, для обработки фатальной ошибки
     *
     * @return bool
     */
    public function endError()
    {
        $error = $this->_error_get_last();

        if ($error) 
        {
            if ( $error['type'] == E_NOTICE ) return true;
            
            if ( $error['type'] == E_WARNING ) return true;
            
            if ( $error['type'] == E_STRICT ) return true;
            
            if ( $error['type'] == E_DEPRECATED ) return true;
            
            $data = array(
                'type'   => 'fatal',
                'error'  =>  $error,
                'env'    => isset($_ENV)    ? $_ENV    : null,
                'post'   => isset($_POST)   ? $_POST   : null,
                'get'    => isset($_GET)    ? $_GET    : null,
                'cookie' => isset($_COOKIE) ? $_COOKIE : null,
                'server' => isset($_SERVER) ? $this->cleanData($_SERVER) : null,
                'files'  => isset($_FILES)  ? $_FILES  : null,
            );
            
            $this->halt($data, $error['type']);
        }
    }

    /**
     * Получение всех логгеров
     *
     * @return \Logger[]
     */
    private function _getLoggers()
    {
        return \Logger::getCurrentLoggers();
    }

    /**
     * Получение данных по ошибке
     *
     * @return array
     */
    public function getData()
    {
        if (!session_id()) {
            @session_start();
        }

        // Получение списка файлов логов
        $files = array();
        foreach ($this->_getLoggers() as $logger) {
            foreach ($logger->getAllAppenders() as $appender) {
                /** @var $appender \LoggerAppender */
                $appender = $logger->getAppender($appender->getName());

                if (is_a($appender, 'LoggerAppenderRollingFile')) {
                    $file                   = $appender->getFileName();
                    $file_data              = file($file, FILE_IGNORE_NEW_LINES);
                    $files[basename($file)] = array_slice($file_data, -1000);
                }
            }
        }

        if ( file_exists(dirname($file).'/php.log') )
        {
            $file_data = file(dirname($file).'/php.log', FILE_IGNORE_NEW_LINES);
            
            $files['php.log'] = array_slice($file_data, -1000);
        }
        
        return array(
            'error' => $_SESSION['error'],
            'files' => $files,
        );
    }

    /**
     * Возвращает объект для формирования zip архивов
     *
     * @return \createZip
     */
    private function _getZip()
    {
        include_once(SERVER_ROOT_PATH . 'ext/zip/createzipfile.php');
        return new \createZip;
    }

    /**
     * Сжатие данных по ошибке в zip архив
     *
     * @return \zipfile
     */
    public function getDataZip()
    {
        $zip = $this->_getZip();

        $data  = $this->getData();
        
        $zip->addFile(var_export($data, true), 'error.txt');

        return $zip->getZippedfile();
    }
    
    function cleanData( $data_array )
    {
        unset($data_array['PHP_AUTH_PW']);
        
        return $data_array;
    }
}
