<?php

class UnitTest extends \PHPUnit\Framework\TestCase
{
    private $ogTags = [
        'og:title' => 'test'
    ];

    /** @test */
    public function parseEucKr()
    {
        $openGraphParser = new \Visualplus\OpenGraph\OpenGraphParser();

        $ogTags = $openGraphParser->parse('./dummy/euc-kr.html');

        $this->assertTrue($this->ogTags == $ogTags);
    }

    /** @test */
    public function parseUtf8()
    {
        $openGraphParser = new \Visualplus\OpenGraph\OpenGraphParser();

        $ogTags = $openGraphParser->parse('./dummy/utf-8.html');

        $this->assertTrue($this->ogTags == $ogTags);
    }

    /** @test */
    public function parseISO8859_1()
    {
        $openGraphParser = new \Visualplus\OpenGraph\OpenGraphParser();

        $ogTags = $openGraphParser->parse('./dummy/iso-8859-1.html');

        $this->assertTrue($this->ogTags == $ogTags);
    }
}