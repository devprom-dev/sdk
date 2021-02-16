<?php

namespace Devprom\ServiceDeskBundle\Tests\Service;
use Devprom\ServiceDeskBundle\Service\SettingsFilter;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SettingsFilterTest extends \PHPUnit\Framework\TestCase {

    /**
     * @test
     */
    public function shouldFilterUnexpectedSettings() {
        $filter = new SettingsFilter(array('expected'));

        $settings = array( 'expected' => 1, 'unexpected' => 2);

        $filtered = $filter->filter($settings);
        $this->assertEquals(1, sizeof($filtered));
        $this->assertFalse(isset($filtered['unexpected']));
        $this->assertTrue(isset($filtered['expected']));
    }

}