<?php

include_once SERVER_ROOT_PATH.'core/classes/system/SanitizeUrl.php';

class TextUtilsTest extends \PHPUnit\Framework\TestCase
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

    public function testSkipHtmlTag()
    {
        $this->assertEquals( '<table><tr><td></td></tr></table>',
            TextUtils::skipHtmlTag('thead', '<table><thead><tr><td></td></tr></thead></table>')
        );
        $this->assertEquals( '<table><tr><td></td></tr></table>',
            TextUtils::skipHtmlTag('thead', '<table><thead style="margin-left:0px;"><tr><td></td></tr></thead></table>')
        );
    }

    public function testStripAnyTags()
    {
        $this->assertEquals( '[F-11] Увеличить',
            TextUtils::stripAnyTags('<a class="uid with-tooltip" tabindex="-1" data-placement="right" data-original-title="" data-content="" info="/pm/0c5b306957a98189f33e451b9aa871a2/tooltip/Feature/11" href="http://devprom.local/pm/0c5b306957a98189f33e451b9aa871a2/F-11">[F-11] Увеличить</a>')
        );
    }
}
