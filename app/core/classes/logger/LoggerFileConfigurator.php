<?php

class LoggerFileConfigurator implements LoggerConfigurator
{
    private $fileName = '';

    function __construct( $fileName ) {
        $this->fileName = $fileName;
    }

    public function configure(LoggerHierarchy $hierarchy, $input = null)
    {
        $layout = new LoggerLayoutPattern();
        $layout->setConversionPattern("\n%d %l %n %m");
        $layout->activateOptions();

        $appFile = new LoggerAppenderFile('foo');
        $appFile->setFile(SERVER_LOGS_PATH . '/' . $this->fileName);
        $appFile->setLayout($layout);
        $appFile->setAppend(true);
        $appFile->setThreshold('debug');
        $appFile->activateOptions();

        $appEcho = new LoggerAppenderEcho('bar');
        $appEcho->setLayout($layout);
        $appEcho->setHtmlLineBreaks(false);
        $appEcho->setThreshold('debug');
        $appEcho->activateOptions();

        $root = $hierarchy->getRootLogger();
        $root->addAppender($appFile);
        $root->addAppender($appEcho);
    }
}