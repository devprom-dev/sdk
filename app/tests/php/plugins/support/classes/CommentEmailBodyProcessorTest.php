<?php

namespace plugins\support\classes;

include_once SERVER_ROOT_PATH . "/plugins/support/classes/CommentEmailBodyProcessor.php";


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class CommentEmailBodyProcessorTest extends \PHPUnit_Framework_TestCase {

    /** @var  CommentEmailBodyProcessor */
    private $processor;

    protected function setUp()
    {
        $this->processor = new CommentEmailBodyProcessor();
    }

    /**
     * @test
     */
    public function shouldNotTrimContentWithoutSeparator() {
        $content = "Some reply without history separator";

        $result = $this->processor->process($content, false);

        $this->assertSame($content, $result);
    }

    /**
     * @test
     */
    public function shouldTrimContentWithSeparator() {
        $meaningfullPart = "Some reply";
        $content =  $meaningfullPart . CommentEmailBodyProcessor::HISTORY_SEPARATOR . "\r\n>Some reply history";

        $result = $this->processor->process($content, false);

        $this->assertSame($meaningfullPart, $result);
    }

    /**
     * @test
     */
    public function shouldTrimContentWithUnicodeSeparator() {
        $meaningfullPart = "Some reply";
        $content =  $meaningfullPart . json_decode('"\u2800"') . "\r\n>Some reply history";

        $result = $this->processor->process($content, false);

        $this->assertSame($meaningfullPart, $result);
    }

    /**
     * @test
     */
    public function shouldTrimHistoryForGmailRussian() {
        $meaningfullPart = "Some reply";
        $content =  $meaningfullPart . "\r\n\r\n\r\n2013/10/9 Devprom Support <support@devprom.ru>\r\n\r\n> **\r\n>\r\n> " .
            json_decode('"\u2800"') . "\r\n>Some reply history";

        $result = $this->processor->process($content, false);

        $this->assertSame($meaningfullPart, $result);
    }

    /**
     * @test
     */
    public function shouldTrimHistoryForGmailEnglish() {
        $meaningfullPart = "Some reply";
        $content =  $meaningfullPart . "\r\n\r\n\r\nOn 01.11.2013 14:49, Devprom Support wrote:\r\n\r\n> **\r\n>\r\n> " .
            json_decode('"\u2800"') . "\r\n>Some reply history";

        $result = $this->processor->process($content, false);

        $this->assertSame($meaningfullPart, $result);
    }

    /**
     * @test
     */
    public function shouldAddHtmlLinebreaks() {
        $content = "Some\r\nmultiline reply";

        $result = $this->processor->process($content, false);

        $this->assertEquals("Some<br />\r\nmultiline reply", $result);
    }

    /**
     * @test
     */
    public function shouldStripOutHtmlTagsIfRequested() {
        $content = "Some reply <div>with tags</div>";

        $result = $this->processor->process($content, true);

        $this->assertEquals("Some reply with tags", $result);
    }

    /**
     * @test
     */
    public function shouldNotStripOutHtmlTagsIfNotRequested() {
        $content = "Some reply <div>with tags</div>";

        $result = $this->processor->process($content, false);

        $this->assertEquals($content, $result);
    }

    /**
     * @test
     */
    public function shouldTrimAdjacentLines() {
        $meaningfullPart = "Some reply\r\n\r\nSecond paragraph";
        $adjacentLines = "\r\n\r\n>On 12:12:12:\r\n>\r\n>";
        $content =  $meaningfullPart . $adjacentLines . CommentEmailBodyProcessor::HISTORY_SEPARATOR . "\r\n>Some reply history";

        $result = $this->processor->process($content, false);

        $this->assertSame("Some reply<br />\r\n<br />\r\nSecond paragraph", $result);
    }

}