<?php

namespace App\Tests\Util;

use App\Util\OpeningHoursUtil;
use PHPUnit\Framework\TestCase;

class OpeningHoursUtilTest extends TestCase
{

    protected
        /** @var OpeningHoursUtil */
        $helper;

    public function setUp()
    {
        $this->helper = new OpeningHoursUtil;
    }

    /**
     * @expectedException \App\Util\Exception\OpeningHoursParseException
     */
    public function testEmpty()
    {
        $this->helper->parse('');
    }

    public function testParseSingleDay()
    {
        $this->assertEquals(
            [1 => [[0, 120]]],
            array_filter($this->helper->parse('Mo 00:00-02:00'))
        );
    }

    public function testParseUnformatted()
    {
        $this->assertEquals(
            [1 => [[0, 120]]],
            array_filter($this->helper->parse('Mo 00:00 - 02:00   '))
        );
    }

    public function testParseMultipleDays()
    {
        $this->assertEquals(
            [
                1 => [[0, 120]],
                2 => [[0, 120]],
            ],
            array_filter($this->helper->parse('Mo,Tu 00:00-02:00'))
        );
    }

    /**
     * @expectedException \App\Util\Exception\OpeningHoursParseException
     */
    public function testInvalidMultipleSameDay()
    {
        $this->helper->parse('Mo,Mo 00:00-02:00');
    }

    /**
     * @expectedException \App\Util\Exception\OpeningHoursParseException
     */
    public function testInvalidTimeRange()
    {
        $this->helper->parse('Mo 09:00-02:00');
    }


    public function testDayRange()
    {
        $this->assertEquals(
            [
                1 => [[0, 120]],
                2 => [[0, 120]],
                3 => [[0, 120]],
            ],
            array_filter($this->helper->parse('Mo-We 00:00-02:00'))
        );
    }

    /**
     * @expectedException \App\Util\Exception\OpeningHoursParseException
     */
    public function testInvalidRange()
    {
        $this->helper->parse('Mo-Mo 00:00-02:00');
    }

    public function testDayRangeOverflow()
    {
        $this->assertEquals(
            [
                0 => [[0, 120]],
                1 => [[0, 120]],
                5 => [[0, 120]],
                6 => [[0, 120]],
            ],
            array_filter($this->helper->parse('Fr-Mo 00:00-02:00'))
        );
    }

    public function testMultipleDays()
    {
        $this->assertEquals(
            [
                1 => [[0, 120]],
                3 => [[0, 120]],
                4 => [[0, 120]],
            ],
            array_filter($this->helper->parse('Mo,We-Th 00:00-02:00'))
        );
    }

    public function testAllDay()
    {
        $this->assertEquals(
            [
                1 => [[0, 60*24]],
                2 => [[0, 60*24]],
                3 => [[0, 60*24]],
            ],
            array_filter($this->helper->parse('Mo-We'))
        );
    }

    public function testMultiIntervals()
    {
        $this->assertEquals(
            [
                1 => [[10*60, 19*60]],
                2 => [[10*60, 19*60]],
                6 => [[10*60, 22*60]],
                0 => [[10*60, 21*60]],
            ],
            array_filter($this->helper->parse([
                "Mo-Tu 10:00-19:00",
                "Sa 10:00-22:00",
                "Su 10:00-21:00"
            ]))
        );
    }

    public function testMultiIntervalsExtendInterval()
    {
        $this->assertEquals(
            [
                1 => [[10*60, 12*60],[15*60,22*60]],
                2 => [[10*60, 12*60]],
            ],
            array_filter($this->helper->parse([
                "Mo-Tu 10:00-12:00",
                "Mo 15:00-22:00",
                "Tu 10:00-11:00"
            ]))
        );
    }

    public function testMergeIntervalsSameLimits()
    {
        $this->assertEquals(
            [
                1 => [[10*60, 22*60]],
                2 => [[10*60, 12*60]],
            ],
            array_filter($this->helper->parse([
                "Mo-Tu 10:00-12:00",
                "Mo 12:00-22:00",
                "Tu 10:00-12:00"
            ]))
        );
    }

}
