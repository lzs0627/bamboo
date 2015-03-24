<?php

namespace IQnote\Bamboo;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testReadAndWrite()
    {
        $time = time();
        Config::set(md5($time), $time);
        $this->assertEquals($time, Config::get(md5($time)), 'Config::set and Config::get');
    }
}
