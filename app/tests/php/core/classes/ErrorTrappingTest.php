<?php
include_once(SERVER_ROOT_PATH . 'core/classes/exceptions/ExceptionHandler.php');

/**
 * Тесты для \core\classes\ExceptionHandlerTest
 *
 * - halt
 * - getErrorTypeStr
 * - captureError
 * - exceptionError
 * - endError
 */
class ExceptionHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Тестирование halt
     *
     * @param array   $data Данные по ошибке
     * @param integer $code Код ошибки
     *
     * @dataProvider providerHalt
     */
    public function testHalt($data, $code)
    {
        /** @var $mock \core\classes\ExceptionHandler|PHPUnit_Framework_TestCase */
        $mock = $this->getMock('\core\classes\ExceptionHandler', array('_exit', '_header'), array(), '', false);
        $mock->expects($this->any())->method('_exit')->will($this->returnValue(true));
        $mock->expects($this->any())->method('_header')->will($this->returnValue(true));

        $mock->halt($data, $code);

        $session = $_SESSION['error'];

        if (!isset($data['id'])) {
            unset($session['id']);
        }

        $this->assertEquals($data, $session);
    }

    /**
     * Провайдер для halt
     *
     * @return array
     */
    public function providerHalt()
    {
        return array(
            array(
                array('type' => 'fatal', 'id' => 123),
                123
            ),
            array(
                array('type' => 'fatal'),
                123
            ),
        );
    }

    /**
     * Тестирование getErrorTypeStr
     */
    public function testGetErrorTypeStr()
    {
        /** @var $mock \core\classes\ExceptionHandler|PHPUnit_Framework_TestCase */
        $mock = $this->getMock('\core\classes\ExceptionHandler', array('_exit', '_header'), array(), '', false);

        $this->assertEquals($mock->getErrorTypeStr(E_USER_WARNING), 'E_USER_WARNING');
    }

    /**
     * Тестирование CaptureError
     *
     * @dataProvider providerCaptureError
     */
    public function testCaptureError($debug_from, $debug_to)
    {
        /** @var $mock \core\classes\ExceptionHandler|PHPUnit_Framework_TestCase */
        $mock = $this->getMock('\core\classes\ExceptionHandler', array('_exit', '_header', '_debug_backtrace'), array(), '', false);
        $mock->expects($this->any())->method('_exit')->will($this->returnValue(true));
        $mock->expects($this->any())->method('_header')->will($this->returnValue(true));
        $mock->expects($this->any())->method('_debug_backtrace')->will($this->returnValueMap(array(array($debug_from))));

        $errno      = E_PARSE;
        $errstr     = 'Моя ошибочка';
        $errfile    = __FILE__;
        $errline    = 10;

        $errcontext = array(
            '_ENV'    => isset($_ENV)    ? $_ENV    : null,
            '_POST'   => isset($_POST)   ? $_POST   : null,
            '_GET'    => isset($_GET)    ? $_GET    : null,
            '_COOKIE' => isset($_COOKIE) ? $_COOKIE : null,
            '_SERVER' => isset($_SERVER) ? $_SERVER : null,
            '_FILES'  => isset($_FILES)  ? $_FILES  : null,
        );

        $mock->captureError($errno, $errstr, $errfile, $errline, $errcontext);

        $session = $_SESSION['error'];

        $data = array(
            'type'   => 'error',
            'error'  => array(
                'errno'   => $errno,
                'message' => $errstr,
                'errfile' => $errfile,
                'errline' => $errline,
            ),
            'debug'  => $debug_to,
            'env'    => $errcontext['_ENV'],
            'post'   => $errcontext['_POST'],
            'get'    => $errcontext['_GET'],
            'cookie' => $errcontext['_COOKIE'],
            'server' => $errcontext['_SERVER'],
            'files'  => $errcontext['_FILES'],
        );

        if (!isset($data['id'])) {
            unset($session['id']);
        }

        $this->assertEquals($data, $session);
    }

    /**
     * Провайдер для testCaptureError
     *
     * @return array
     */
    public function providerCaptureError()
    {
        return array(
            array(
                // debug_from
                array(
                    array('Удалится'),
                    array(
                        'file'     => 'D:\var\www\devprom\feature-a\dev\apache\htdocs\cache\symfony2\classes.php',
                        'line'     => 4532,
                        'function' => 'call_user_func',
                        'args'     => array(),
                    ),
                    array(
                        'file'     => 'D:\var\www\devprom\feature-a\dev\apache\htdocs\cache\symfony2\classes.php',
                        'line'     => 4446,
                        'function' => 'doDispatch',
                        'class'    => 'Symfony\Component\EventDispatcher\EventDispatcher',
                        'object'   => new StdClass,
                        'type'     => '->',
                        'args'     => array(),
                    ),
                ),
                // debug_to
                array(
                    1 => array(
                        'file'     => 'D:\var\www\devprom\feature-a\dev\apache\htdocs\cache\symfony2\classes.php',
                        'line'     => 4532,
                        'function' => 'call_user_func',
                        'args'     => array(),
                    ),
                    2 => array(
                        'file'     => 'D:\var\www\devprom\feature-a\dev\apache\htdocs\cache\symfony2\classes.php',
                        'line'     => 4446,
                        'function' => 'doDispatch',
                        'class'    => 'Symfony\Component\EventDispatcher\EventDispatcher',
                        'type'     => '->',
                        'args'     => array(),
                    ),
                )
            ),
        );
    }

    /**
     * Тестирование exceptionError
     *
     * @param array   $trace   Трассировка
     * @param string  $message Текст ошибки
     * @param integer $code    Код ошибки
     * @param string  $file    Файл
     * @param integer $line    Строка в файле
     * @param array   $result  Данные которые должны получиться
     *
     * @dataProvider providerExceptionError
     */
    public function testExceptionError($trace, $message, $code, $file, $line, $result)
    {
        /** @var $mock \core\classes\ExceptionHandler */
        $mock = $this->getMock('\core\classes\ExceptionHandler', array('_exit', '_header', '_debug_backtrace'), array(), '', false);
        $mock->expects($this->any())->method('_exit')->will($this->returnValue(true));
        $mock->expects($this->any())->method('_header')->will($this->returnValue(true));

        /** @var $mock_e \Exception */
        $mock_e = $this->getMock('\stdClass', array('getTrace', 'getMessage', 'getCode', 'getFile', 'getLine'));  //, array($message, $code)
        $mock_e->expects($this->any())->method('getTrace')->will($this->returnValue($trace));
        $mock_e->expects($this->any())->method('getMessage')->will($this->returnValue($message));
        $mock_e->expects($this->any())->method('getCode')->will($this->returnValue($code));
        $mock_e->expects($this->any())->method('getFile')->will($this->returnValue($file));
        $mock_e->expects($this->any())->method('getLine')->will($this->returnValue($line));

        $mock->exceptionError($mock_e);

        $session = $_SESSION['error'];

        if (!isset($data['id'])) {
            unset($session['id']);
        }

        $this->assertEquals($result, $session);
    }

    /**
     * Провайдер для testCaptureError
     *
     * @return array
     */
    public function providerExceptionError()
    {
        return array(
            array(
                array(
                    array(),
                    array(
                        'file'     => 'D:\var\www\devprom\feature-a\dev\apache\htdocs\cache\symfony2\classes.php',
                        'line'     => 4532,
                        'function' => 'call_user_func',
                        'args'     => array(
                            'Symfony\Component\HttpKernel\Event\GetResponseEvent',
                            array(
                                new stdClass,
                                1
                            ),
                            new stdClass,
                        ),
                        'object' => new stdClass,
                    ),
                    array(
                        'file'     => 'D:\var\www\devprom\feature-a\dev\apache\htdocs\cache\symfony2\classes.php',
                        'line'     => 4446,
                        'function' => 'doDispatch',
                        'class'    => 'Symfony\Component\EventDispatcher\EventDispatcher',
                        'type'     => '->',
                        'args'     => array(
                            'kernel.request',
                            'Symfony\Component\HttpKernel\Event\GetResponseEvent',
                            array(),
                        ),
                    ),
                ),
                'message',
                1024,
                'file.php',
                1,

                array(
                    'type' => 'exception',
                    'error' => array(
                        'message' => 'message',
                        'code'    => 1024,
                        'file'    => 'file.php',
                        'line'    => 1,
                    ),
                    'debug'  => array(
                        1 => array(
                            'file'     => 'D:\var\www\devprom\feature-a\dev\apache\htdocs\cache\symfony2\classes.php',
                            'line'     => 4532,
                            'function' => 'call_user_func',
                            'args'     => array(
                                'Symfony\Component\HttpKernel\Event\GetResponseEvent',
                                '[array]',
                                'stdClass',
                            ),
                        ),
                        2 => array(
                            'file'     => 'D:\var\www\devprom\feature-a\dev\apache\htdocs\cache\symfony2\classes.php',
                            'line'     => 4446,
                            'function' => 'doDispatch',
                            'class'    => 'Symfony\Component\EventDispatcher\EventDispatcher',
                            'type'     => '->',
                            'args'     => array(
                                'kernel.request',
                                'Symfony\Component\HttpKernel\Event\GetResponseEvent',
                                '[array]',
                            ),
                        )
                    ),
                    'env'    => isset($_ENV)    ? $_ENV    : null,
                    'post'   => isset($_POST)   ? $_POST   : null,
                    'get'    => isset($_GET)    ? $_GET    : null,
                    'cookie' => isset($_COOKIE) ? $_COOKIE : null,
                    'server' => isset($_SERVER) ? $_SERVER : null,
                    'files'  => isset($_FILES)  ? $_FILES  : null,
                )
            ),
        );
    }

    /**
     * Тестирование endError
     *
     * @param $error
     * @param $result
     *
     * @dataProvider providerEndError
     */
    public function testEndError($error, $result)
    {
        /** @var $mock \core\classes\ExceptionHandler|PHPUnit_Framework_TestCase */
        $mock = $this->getMock('\core\classes\ExceptionHandler', array('_exit', '_header', '_error_get_last'), array(), '', false);
        $mock->expects($this->any())->method('_exit')->will($this->returnValue(true));
        $mock->expects($this->any())->method('_header')->will($this->returnValue(true));
        $mock->expects($this->any())->method('_error_get_last')->will($this->returnValue($error));

        $mock->endError();

        $session = $_SESSION['error'];

        unset($session['id']);

        $this->assertEquals($result, $session);
    }

    public function providerEndError()
    {
        return array(
            array(
                array(
                    'type' => 1,
                ),
                array(
                    'type'   => 'fatal',
                    'error'  => array(
                        'type' => 1,
                    ),
                    'env'    => isset($_ENV)    ? $_ENV    : null,
                    'post'   => isset($_POST)   ? $_POST   : null,
                    'get'    => isset($_GET)    ? $_GET    : null,
                    'cookie' => isset($_COOKIE) ? $_COOKIE : null,
                    'server' => isset($_SERVER) ? $_SERVER : null,
                    'files'  => isset($_FILES)  ? $_FILES  : null,
                )
            ),
            array(
                null,
                null,
            ),
        );
    }
    
    public function testErrorsToBeSkipped()
    {
        $mock = $this->getMock('\core\classes\ExceptionHandler', array('_exit', '_header', '_error_get_last'), array(), '', false);
        
        $this->assertTrue( is_bool($mock->captureError(E_NOTICE, '', '', '', '')) );
        $this->assertTrue( is_bool($mock->captureError(E_WARNING, '', '', '', '')) );
        $this->assertTrue( is_bool($mock->captureError(E_STRICT, '', '', '', '')) );
        $this->assertTrue( is_bool($mock->captureError(E_DEPRECATED, '', '', '', '')) );
        $this->assertTrue( is_bool($mock->captureError('8192', '', '', '', '')) );
        $this->assertFalse( is_bool($mock->captureError(E_ERROR, '', '', '', '')) );
    }
}