<?php

namespace Metroplex\Edifact\Tests;

use Metroplex\Edifact\Message;

class CompleteTest extends \PHPUnit_Framework_TestCase
{

    public function messageProvider()
    {
        $path = __DIR__ . "/data";
        return [
        	["{$path}/wikipedia.edi"],
         	["{$path}/order.edi"]
		];
    }
    /**
     * @dataProvider messageProvider
     */
    public function testFormat($file)
    {
        $output = (string) Message::fromFile($file);

        $message = file_get_contents($file);
        $expected = str_replace("\n", "", $message);

        $this->assertSame($expected, $output);
    }
}
