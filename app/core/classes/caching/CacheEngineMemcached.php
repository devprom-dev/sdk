<?php

include_once "CacheEngine.php";

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class CacheEngineMemcached extends CacheEngine {

    /**
     * @var Memcache
     */
    private $memcache;

    function __construct()
    {
        $this->memcache = new Memcache();
        $this->memcache->addserver("localhost");
    }

    function get($key, $category)
    {
        $val = $this->memcache->get($this->getNamespace($category) . $key);
        return $val ? $val : '';
    }

    function set($key, $value, $category)
    {
        $this->memcache->set($this->getNamespace($category) . $key, $value);
    }

    function reset($key, $category)
    {
        $this->memcache->delete($this->getNamespace($category) . $key);
    }

    function truncate($category)
    {
        $namespace = $this->getNamespace($category);
        $this->memcache->set($category, ++$namespace);
    }

    function drop()
    {
        $this->memcache->flush();
        sleep(1); // flush has 1 second granularity
    }

    /**
     * @param $category
     * @return array|int|string
     */
    protected function getNamespace($category)
    {
        $namespace = $this->memcache->get($category);
        if ($namespace === false) {
            $namespace = time();
            $this->memcache->add($category, $namespace);
            return $namespace;
        }
        return $namespace;
    }


}