<?php

include_once SERVER_ROOT_PATH.'/core/classes/caching/CacheEngineShmop.php';

class CacheEngineShmopTest extends PHPUnit_Framework_TestCase {

    /**
     * @var CacheEngineMemcached
     */
    private $engine;

    public function setUp() {
    	$this->markTestSkipped("Needs memcache service running");
        $this->engine = new CacheEngineShmop();
    }

    public function testSet() {
        $this->engine->set("key", 22, "cat");
    }

    public function testGet() {
        $this->engine->set("key", 22, "cat");
        $val = $this->engine->get("key", "cat");
        $this->assertEquals(22, $val);
    }

    public function testCategorySet() {
        $this->engine->truncate("cat1");
        $this->engine->set("key", 22, "cat1");
        $val = $this->engine->get("key", "cat2");
        $this->assertEquals('', $val);
    }

    public function testReset() {
        $this->engine->set("key", 22, "cat");

        $this->engine->reset('key', 'cat');

        $val = $this->engine->get("key", "cat");
        $this->assertEquals('', $val);
    }

    public function testTruncate() {
        $this->engine->set("key1", 22, "cat1");
        $this->engine->set("key2", 33, "cat1");
        $this->engine->set("key3", 44, "cat2");

        $this->engine->truncate('cat1');

        $this->assertEquals('', $this->engine->get("key1", "cat1"));
        $this->assertEquals('', $this->engine->get("key2", "cat1"));
        $this->assertEquals(44, $this->engine->get("key3", "cat2"));
    }

    public function testDrop() {
        $this->engine->set("key1", 22, "cat1");
        $this->engine->set("key2", 44, "cat2");

        $this->engine->drop();

        $this->assertEquals('', $this->engine->get("key1", "cat1"));
        $this->assertEquals('', $this->engine->get("key2", "cat2"));
    }
}