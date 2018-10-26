<?php

include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/converters/WikiImporterEngine.php";

class WikiImporterEngineTest extends DevpromDummyTestCase
{
    function setUp() {
        parent::setUp();
    }
    
    function testTableStriptHeaders()
    {
        $html = '<table id="asd"><th>header</th><td id="asd"><h2 id="asd">text2</h2>'.PHP_EOL.'</td></table><table><th>header</th><td><h1>text1</h1></td></table>';
        WikiImporterEngine::stripHeaders($html);
        $this->assertContains('<p>text1</p>', $html);
        $this->assertContains('<p>text2</p>', $html);
    }

    function testColumnStriptHeaders()
    {
        $html = '<table><td>asd'.PHP_EOL.'<h6 id="авторподписантотправительфилиал-дата-получения">&quot;Автор/Подписант/Отправитель/Филиал/ Дата получения&quot;</h6> text'.PHP_EOL.' <h6>asd</h6></td></table>';
        WikiImporterEngine::stripHeaders($html);
        $this->assertNotContains('h6', $html);
        $this->assertContains('asd', $html);
    }

    function testFalseStriptHeaders()
    {
        $html = '<table><td id="asd"><h2>text2</h2></td></table>'.PHP_EOL.'<h3>header3</h3>'.PHP_EOL.'<table><th>header</th><td><h1 id="asd">text1</h1></td></table>';
        WikiImporterEngine::stripHeaders($html);
        $this->assertContains('<p>text1</p>', $html);
        $this->assertContains('<h3>header3</h3>', $html);
    }

    function testEmbeddedTableStriptHeaders()
    {
        $html = '<table><td>text <h2 id="asd">text2</h2>'.PHP_EOL.'<table><td><p>text</p></td></table></td></table>';
        WikiImporterEngine::stripHeaders($html);
        $this->assertNotContains('h2', $html);
    }
}