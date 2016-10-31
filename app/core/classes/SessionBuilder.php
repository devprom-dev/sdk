<?php
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

class SessionBuilder
{
    const CACHE_KEY = 'sessions';
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
        $session = null;
    }

    public function invalidate( $sessionId ) {
        if ( $sessionId == "" ) {
            getFactory()->getCacheService()->truncate(self::CACHE_KEY);
        }
        else {
            getFactory()->getCacheService()->set($sessionId, null, self::CACHE_KEY);
        }
    }

    protected function __construct() {
    }

    protected function buildSession( array $parms, $cacheService = null ) {
        return new SessionBase();
    }
}