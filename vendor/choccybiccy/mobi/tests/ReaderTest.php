<?php

namespace Choccybiccy\Mobi;

/**
 * Class ReaderTest.
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test construct.
     */
    public function testConstruct()
    {
        $mobi = new Reader($this->getMobiPath());
        $this->assertInstanceOf('\SplFileObject', $mobi->getFile());
    }

    /**
     * Test parse runs parsing sub-routines.
     */
    public function testParse()
    {
        $methods = ['checkFormat', 'parsePalmDb', 'parsePalmDoc', 'parseMobiHeader', 'parseExth'];
        $reader = $this->getMockReader($methods);
        foreach ($methods as $method) {
            $reader->expects($this->once())
                ->method($method);
        }
        $this->runProtectedMethod($reader, 'parse');
    }

    /**
     * @return string
     */
    protected function getMobiPath()
    {
        return realpath(__DIR__.'/../resources/sherlock.mobi');
    }

    /**
     * @param array|null $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Reader
     */
    protected function getMockReader(array $methods = null)
    {
        return $this->getMockBuilder(Reader::class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param object $object
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    protected function runProtectedMethod($object, $method, array $arguments = null)
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        if ($arguments) {
            $method->invokeArgs($object, $arguments);
        } else {
            $method->invoke($object);
        }
    }

    /**
     * @param array $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\SplFileObject
     */
    protected function getMockFile($methods = [])
    {
        return $this->getMockBuilder('\SplFileObject')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
