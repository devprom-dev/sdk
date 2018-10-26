<?php

include_once SERVER_ROOT_PATH.'core/classes/system/SanitizeUrl.php';

class TextUtilsTest extends PHPUnit_Framework_TestCase
{
	public function testHashIds()
	{
	    $this->assertEquals( array(14909),
            TextUtils::parseIds(TextUtils::buildIds(array(14909)))
        );
	    $this->assertEquals( array(),
            TextUtils::parseIds('devtest,TaskType_59ca44bde60e1,development')
        );
	}

	public function testRemoveHtmlTag()
    {
        $this->assertEquals( '<table></table>',
            TextUtils::removeHtmlTag('colgroup', '<table><colgroup/></table>')
        );
        $this->assertEquals( '<table></table>',
            TextUtils::removeHtmlTag('colgroup', '<table><colgroup span="f"/></table>')
        );
        $this->assertEquals( '<table></table>',
            TextUtils::removeHtmlTag('colgroup', '<table><colgroup span="f"><col>asd</col></colgroup></table>')
        );
        $this->assertEquals( '<table></table><table></table>',
            TextUtils::removeHtmlTag('colgroup', '<table><colgroup span="f"><col>asd</col></colgroup></table><table><colgroup span="f"><col>asd</col></colgroup></table>')
        );
    }
}
