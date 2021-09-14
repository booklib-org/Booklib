<?php

namespace Choccybiccy\Mobi\Header\Record;

/**
 * Class ExthRecord.
 */
class ExthRecord implements RecordInterface
{
    /**
     * @var int
     */
    protected $type;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * ExthRecord constructor.
     *
     * @param int   $type
     * @param int   $length
     * @param mixed $data
     */
    public function __construct($type, $length, $data)
    {
        $this->type = $type;
        $this->length = $length;
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
