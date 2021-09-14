<?php

namespace Choccybiccy\Mobi\Header;

use Choccybiccy\Mobi\TestCase;

/**
 * Class ExthHeaderTest.
 */
class ExthHeaderTest extends TestCase
{
    /**
     * Test getters.
     */
    public function testGetters()
    {
        $getters = [
            'length' => mt_rand(1, 10000),
        ];
        $header = new ExthHeader($getters['length']);
        foreach ($getters as $method => $value) {
            $method = 'get'.ucfirst($method);
            self::assertEquals($value, $header->{$method}());
        }
    }

    /**
     * Test getRecordByType.
     */
    public function testGetRecordByType()
    {
        $record = $this->getMockRecord(['type' => ExthHeader::TYPE_AUTHOR, 'data' => 'something']);
        $header = new ExthHeader(0, [$record]);
        self::assertEquals('something', $header->getRecordByType(ExthHeader::TYPE_AUTHOR));
    }

    /**
     * Test getRecordByType throws exception if record doesn't exist.
     *
     * @expectedException \Choccybiccy\Mobi\Exception\NoSuchRecordException
     */
    public function testGetRecordByTypeThrowsException()
    {
        (new ExthHeader(0))->getRecordByType(ExthHeader::TYPE_AUTHOR);
    }
}
