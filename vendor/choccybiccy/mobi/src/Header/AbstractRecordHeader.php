<?php

namespace Choccybiccy\Mobi\Header;

use Choccybiccy\Mobi\Header\Record\RecordInterface;

/**
 * Class AbstractRecordHeader.
 */
abstract class AbstractRecordHeader implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $records = [];

    /**
     * AbstractRecordHeader constructor.
     *
     * @param array $records
     */
    public function __construct(array $records = [])
    {
        $this->records = $records;
    }

    /**
     * @return \ArrayIterator|RecordInterface[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->records);
    }

    /**
     * @param RecordInterface $record
     *
     * @return $this
     */
    public function addRecord(RecordInterface $record)
    {
        $this->records[] = $record;

        return $this;
    }
}
