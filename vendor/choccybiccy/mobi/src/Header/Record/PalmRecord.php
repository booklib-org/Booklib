<?php

namespace Choccybiccy\Mobi\Header\Record;

/**
 * Class PalmRecord.
 */
class PalmRecord implements RecordInterface
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $attributes;

    /**
     * @var int
     */
    protected $id;

    /**
     * PalmRecord constructor.
     *
     * @param int $offset
     * @param int $attributes
     * @param int $id
     */
    public function __construct($offset, $attributes, $id)
    {
        $this->offset = $offset;
        $this->attributes = $attributes;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
