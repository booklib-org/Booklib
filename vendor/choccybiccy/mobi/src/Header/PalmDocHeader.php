<?php

namespace Choccybiccy\Mobi\Header;

/**
 * Class PalmDocHeader.
 */
class PalmDocHeader
{
    const COMPRESSION_NONE = 1;

    const COMPRESSION_PALMDOC = 2;

    const COMPRESSION_HUFFCDIC = 17480;

    const ENCRYPTION_NONE = 0;

    const ENCRYPTION_OLD_MOBIPOCKER = 1;

    const ENCRYPTION_MOBIPOCKET = 2;

    /**
     * @var int
     */
    protected $compression;

    /**
     * @var int
     */
    protected $textLength;

    /**
     * @var int
     */
    protected $recordCount;

    /**
     * @var int
     */
    protected $recordSize;

    /**
     * @var int
     */
    protected $encryption;

    /**
     * PalmDoc constructor.
     *
     * @param int $compression
     * @param int $textLength
     * @param int $recordCount
     * @param int $recordSize
     * @param int $encryption
     */
    public function __construct($compression, $textLength, $recordCount, $recordSize, $encryption)
    {
        $this->compression = $compression;
        $this->textLength = $textLength;
        $this->recordCount = $recordCount;
        $this->recordSize = $recordSize;
        $this->encryption = $encryption;
    }

    /**
     * @return int
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * @return int
     */
    public function getTextLength()
    {
        return $this->textLength;
    }

    /**
     * @return int
     */
    public function getRecordCount()
    {
        return $this->recordCount;
    }

    /**
     * @return int
     */
    public function getRecordSize()
    {
        return $this->recordSize;
    }

    /**
     * @return int
     */
    public function getEncryption()
    {
        return $this->encryption;
    }
}
