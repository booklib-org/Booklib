<?php

namespace Choccybiccy\Mobi;

use Choccybiccy\Mobi\Header\Record\RecordInterface;

/**
 * Class TestCase.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array|null $data
     * @param array|null $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|RecordInterface
     */
    protected function getMockRecord(array $data = null, array $methods = null)
    {
        if ($data) {
            foreach (array_keys($data) as $key) {
                $method = 'get'.ucfirst($key);
                $methods[] = $method;
            }
        }
        $record = $this->getMockBuilder(RecordInterface::class)->setMethods($methods)->getMock();
        if ($data) {
            foreach ($data as $key => $value) {
                $method = 'get'.ucfirst($key);
                $record->expects($this->once())
                    ->method($method)
                    ->willReturn($value);
            }
        }

        return $record;
    }
}
