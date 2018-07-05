<?php
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

class SessionBuilder
{
    const CACHE_KEY = 'sessions';
    const max_requests = 30;
    protected static $singleInstance = null;
    protected static $nativeSession = null;

    public static function Instance()
    {
        if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        static::$nativeSession = new Session(
            new NativeSessionStorage(
                array(
                    "name" => "devprom-app"
                ),
                new NullSessionHandler()
            )
        );
        static::$nativeSession->start();
        return static::$singleInstance = new static();
    }

    public function openSession( array $parms = array(), $cacheService = null )
    {
        global $session;

        if ( is_dir($this->dirName) ) {
            try {
                if ( iterator_count(new FilesystemIterator($this->dirName, FilesystemIterator::SKIP_DOTS)) > $this->maxRequestsPerSession ) {
                    exit(header('Location: /503'));
                }
            }
            catch( \Exception $e ) {}
        }

        $nativeSessionId = md5(get_class($this).serialize($parms).self::$nativeSession->getId());
        if ( self::$nativeSession->getId() != "" ) {
            $session = getFactory()->getCacheService()->get($nativeSessionId, self::CACHE_KEY);
            if ( is_object($session) ) return $session;
        }

        $session = $this->buildSession($parms, $cacheService);
        $session->setId( $nativeSessionId );
        $session->getUserIt();
        $this->persist($session);

        return $session;
    }

    public function persist( $session ) {
        getFactory()->getCacheService()->set($session->getId(), $session, self::CACHE_KEY);
    }

    public function close()
    {
        global $session;
        self::$nativeSession->invalidate(1);
        setcookie('devprom-app', '', 0, '/' );
        if ( is_object($session) ) {
            $session->close();
            $this->invalidate($session->getId());
        }
        setcookie('devprom', '', 0, '/', $_SERVER['HTTP_HOST'] );
        setcookie('devprom', '', 0, '/' );
        $session = null;
    }

    public function invalidate( $sessionId ) {
        if ( $sessionId == "" ) {
            getFactory()->getCacheService()->invalidate(self::CACHE_KEY);
        }
        else {
            getFactory()->getCacheService()->set($sessionId, null, self::CACHE_KEY);
        }
    }

    protected function __construct()
    {
        $this->maxRequestsPerSession = defined('MAX_REQUESTS_PER_SESSION') ? MAX_REQUESTS_PER_SESSION : self::max_requests;
        $this->dirName = SERVER_FILES_PATH . 'sessions/' . static::$nativeSession->getId();
        $this->fileName = tempnam($this->dirName, 'session');

        @mkdir($this->dirName, 0777, true);
        file_put_contents($this->fileName, '');
    }

    function __destruct() {
        if ( file_exists($this->fileName) ) unlink($this->fileName);
    }

    protected function buildSession( array $parms, $cacheService = null ) {
        return new SessionBase();
    }

    private $maxRequestsPerSession;
    private $fileName = '';
    private $dirName = '';
}