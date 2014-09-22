<?php

namespace Devprom\CommonBundle\Doctrine\DBAL;

use Doctrine\DBAL\Configuration,
    Doctrine\DBAL\Driver,
    Doctrine\Common\EventManager,
    Doctrine\DBAL\Cache\QueryCacheProfile;


/**
 * @see http://www.doctrine-project.org/jira/browse/DBAL-275
 */
class Connection extends \Doctrine\DBAL\Connection
{
    protected $reconnectAttempts = 0;

    public function __construct(array $params, Driver $driver, Configuration $config = null, EventManager $eventManager = null)
    {
        if (isset($params['driverOptions']['x_reconnect_attempts']) && method_exists($driver, 'getReconnectExceptions')) {
            // sanity check: 0 if no exceptions are available
            $reconnectExceptions = $driver->getReconnectExceptions();
            $this->reconnectAttempts = empty($reconnectExceptions) ? 0 : (int)$params['driverOptions']['x_reconnect_attempts'];
        }
        parent::__construct($params, $driver, $config, $eventManager);
    }

    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
    {
        $stmt = null;
        $attempt = 0;
        $retry = true;
        while ($retry) {
            $retry = false;
            try {
                $stmt = parent::executeQuery($query, $params, $types);
            } catch (\Exception $e) {
                if ($this->validateReconnectAttempt($e, $attempt)) {
                    $this->close();
                    $attempt++;
                    $retry = true;
                } else {
                    throw $e;
                }
            }
        }
        return $stmt;
    }

    public function query()
    {
        $stmt = null;
        $args = func_get_args();
        $attempt = 0;
        $retry = true;
        while ($retry) {
            $retry = false;
            try {
                // max arguments is 4 -> anything is better then calling call_user_func_array()!
                switch (count($args)) {
                    case 1:
                        $stmt = parent::query($args[0]);
                        break;
                    case 2:
                        $stmt = parent::query($args[0], $args[1]);
                        break;
                    case 3:
                        $stmt = parent::query($args[0], $args[1], $args[2]);
                        break;
                    case 4:
                        $stmt = parent::query($args[0], $args[1], $args[2], $args[3]);
                        break;
                    default:
                        $stmt = parent::query();
                    // no break

                }
            } catch (\Exception $e) {
                if ($this->validateReconnectAttempt($e, $attempt)) {
                    $this->close();
                    $attempt++;
                    $retry = true;
                } else {
                    throw $e;
                }
            }
        }
        return $stmt;
    }

    public function executeUpdate($query, array $params = array(), array $types = array())
    {
        $stmt = null;
        $attempt = 0;
        $retry = true;
        while ($retry) {
            $retry = false;
            try {
                $stmt = parent::executeUpdate($query, $params, $types);
            } catch (\Exception $e) {
                if ($this->validateReconnectAttempt($e, $attempt)) {
                    $this->close();
                    $attempt++;
                    $retry = true;
                } else {
                    throw $e;
                }
            }
        }
        return $stmt;
    }

    public function prepare($sql)
    {
        return $this->prepareWrapped($sql);
    }

    protected function prepareWrapped($sql)
    {
        // returns a reconnect-wrapper for Statements
        return new Statement($sql, $this);
    }

    /**
     * do not use, only used by Statement-class
     *
     * needs to be public for access from the Statement-class
     *
     * @deprecated
     */
    public function prepareUnwrapped($sql)
    {
        // returns the actual statement
        return parent::prepare($sql);
    }

    public function validateReconnectAttempt(\Exception $e, $attempt)
    {
        if ($this->getTransactionNestingLevel() < 1 && $this->reconnectAttempts && $attempt < $this->reconnectAttempts) {
            $reconnectExceptions = $this->_driver->getReconnectExceptions();
            $message = $e->getMessage();
            if (!empty($reconnectExceptions)) {
                foreach ($reconnectExceptions as $reconnectException) {
                    if (strpos($message, $reconnectException) !== false) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}