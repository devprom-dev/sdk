<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/parsers/WikiParser.php";

class WikiParserTest extends DevpromTestCase
{
    function testParseUid()
    {   
        $parser = $this->getMock('WikiParser', array('getUidInfo'), array(null));
        
        $parser->expects($this->any())->method('getUidInfo')->will( $this->returnValueMap(
                array (
                        array ( 'K-1', array('url' => 'http://localhost/pm/devprom/K-1', 'caption' => 'Caption', 'uid' => 'K-1') ),
                        array ( 'K-2', array() ),
                        array ( 'K-3', array('url' => 'http://localhost/pm/devprom/K-3', 'caption' => 'Caption', 'uid' => 'K-3', 'completed' => true) )
                ) 
        ));
        
        $string = '<p><a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a></p>';
        $this->assertEquals(
                $string, preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), $string)
        );

        $string = '<p><a class="uid" href="http://localhost/pm/devprom/K-1">[<strike>K-1</strike>] Caption</a></p>';
        $this->assertContains(
                $string, preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), $string)
        );
        
        $this->assertContains(
                'uhha <a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a>',
                preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), 'uhha K-1')
        );
        
        $this->assertContains(
                'uhha <a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a>',
                preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), 'uhha [K-1]')
        );
        
        $this->assertContains(
                '<a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a>',
                preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), 'K-1')
        );
        
        $this->assertContains(
                '<p><a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a></p>',
                preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), '<p>K-1</p>')
        );

        $this->assertContains(
            '<span><a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a></span>',
            preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), '<span>K-1</span>')
        );

        $this->assertContains(
                '<p>K-2</p>', 
                preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), '<p>K-2</p>')
        );

        $this->assertContains(
                '<p><a class="uid" href="http://localhost/pm/devprom/K-3">[<strike>K-3</strike>] Caption</a></p>',
                preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), '<p>K-3</p>')
        );

        $this->assertContains(
            '<p><ol><li><a class="uid" href="http://localhost/pm/devprom/K-3">[<strike>K-3</strike>] Caption</a></li></ol></p>',
            preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), '<p><ol><li>K-3</li></ol></p>')
        );

        $this->assertContains(
            '<p><ol><li>text before <a class="uid" href="http://localhost/pm/devprom/K-3">[<strike>K-3</strike>] Caption</a> text after</li></ol></p>',
            preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), '<p><ol><li>text before K-3 text after</li></ol></p>')
        );

        $this->assertContains(
            '<p><ol><li>text before <a class="uid" href="http://localhost/pm/devprom/K-3">[<strike>K-3</strike>] Caption</a> text after <a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a></li></ol></p>',
            preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), '<p><ol><li>text before K-3 text after K-1</li></ol></p>')
        );

        $this->assertContains(
            preg_replace('/[\r\n]|\s{2,}/', '',
                '<p><ol>
                    <li>text before <a class="uid" data-cke-saved-href="http://localhost/pm/devprom/K-1" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a> text after <a class="uid" data-cke-saved-href="http://localhost/pm/devprom/K-1" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a></li>
                    <li>text <a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a> text <a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a>.</li>
                </ol></p>'
            ),
            preg_replace_callback(REGEX_UID, array($parser, 'parseUidCallback'), '<p><ol><li>text before <a class="uid" data-cke-saved-href="http://localhost/pm/devprom/K-1" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a> text after <a class="uid" data-cke-saved-href="http://localhost/pm/devprom/K-1" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a></li><li>text K-1 text K-1.</li></ol></p>')
        );
    }

    function testUpdateUid()
    {
        $parser = $this->getMock('WikiParser', array('getUidInfo'), array(null));
        $parser->expects($this->any())->method('getUidInfo')->will( $this->returnValueMap(
            array (
                array ( 'K-1', array('url' => 'http://localhost/pm/devprom/K-1', 'caption' => 'Caption', 'uid' => 'K-1') )
            )
        ));

        $this->assertContains(
            preg_replace('/[\r\n]|\s{2,}/', '',
                'http://127.0.0.1/pm/devprom/K-2'
            ),
            preg_replace_callback(REGEX_UPDATE_UID, array($parser, 'parseUpdateUidCallback'), 'http://127.0.0.1/pm/devprom/K-2')
        );

        $this->assertContains(
            preg_replace('/[\r\n]|\s{2,}/', '',
                '<p><ol>
                    <li>text before <a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a> text after <a class="uid" href="http://localhost/pm/devprom/K-1">[K-1] Caption</a></li>
                    <li>text K-1 text K-1.</li>
                </ol></p>'
            ),
            preg_replace_callback(REGEX_UPDATE_UID, array($parser, 'parseUpdateUidCallback'), '<p><ol><li>text before <a class="uid" href="http://127.0.0.1/pm/devprom/K-1">[K-1] Caption</a> text after <a class="uid" href="http://127.0.0.1/pm/devprom/K-1">[K-1] Caption</a></li><li>text K-1 text K-1.</li></ol></p>')
        );
    }

    function testParseIncludePage()
    {   
        $entity = $this->getMock('WikiPage', array('getExact', 'resetFilters'));
        
        $entity->expects($this->any())->method('getExact')->will( $this->returnValueMap(
                array (
                        array ( 1, $entity->createCachedIterator(array(
                            array( 'WikiPageId' => '1', 'Content' => 'Included content' )
                        ))),
                ) 
        ));
        
        $page_it = $entity->getExact(1);
        
        $parser = $this->getMock('WikiParser', array('getUidInfo'), array(null));
        
        $parser->expects($this->any())->method('getUidInfo')->will( $this->returnValueMap(
                array (
                        array ( 'K-1', array( 'object_it' => $page_it ))
                ) 
        ));
        
        $this->assertContains(
                'Included content', 
                preg_replace_callback(REGEX_INCLUDE_PAGE, array($parser, 'parseIncludePageCallback'), '{{K-1}}')
        );
    }
        
    function testNoteSimple()
    {
        $parser = $this->getMock('WikiParser', array('getUidInfo'), array(null));
        
        $this->assertContains(
                '<div class="alert alert-warning">Simple note</div>', 
                $parser->parse("[note=Simple note]")
        );
    }

    function testNoteHuge()
    {
        $parser = $this->getMock('WikiParser', array('getUidInfo'), array(null));
        
        $this->assertContains(
                '<div class="alert alert-warning">Huge note</div>', 
                $parser->parse("[note]Huge note[/note]")
        );
    }

    function testImportantSimple()
    {
        $parser = $this->getMock('WikiParser', array('getUidInfo'), array(null));
        
        $this->assertContains(
                '<div class="alert alert-error">Simple note</div>', 
                $parser->parse("[important=Simple note]")
        );
    }

    function testImportantHuge()
    {
        $parser = $this->getMock('WikiParser', array('getUidInfo'), array(null));
        
        $this->assertContains(
                '<div class="alert alert-error">Huge note</div>', 
                $parser->parse("[important]Huge note[/important]")
        );
    }
}