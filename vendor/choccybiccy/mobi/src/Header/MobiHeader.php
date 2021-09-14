<?php

namespace Choccybiccy\Mobi\Header;

/**
 * Class MobiHeader.
 */
class MobiHeader
{
    const TYPE_MOBIPOCKET_BOOK = 2;

    const TYPE_PALMDOC_BOOK = 3;

    const TYPE_AUDIO = 4;

    const TYPE_MOBIPOCKER_KINDLEGEN = 232;

    const TYPE_KF8 = 248;

    const TYPE_NEWS = 257;

    const TYPE_NEWS_FEED = 258;

    const TYPE_PICS = 513;

    const TYPE_WORD = 514;

    const TYPE_XLS = 515;

    const TYPE_PPT = 516;

    const TYPE_TEXT = 517;

    const TYPE_HTML = 518;

    const ENCODING_CP1252 = 1252;

    const ENCODING_UTF8 = 65001;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int
     */
    protected $encoding;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $version;

    /**
     * MobiHeader constructor.
     *
     * @param int $length
     * @param int $type
     * @param int $encoding
     * @param int $id
     * @param int $version
     */
    public function __construct($length, $type, $encoding, $id, $version)
    {
        $this->length = $length;
        $this->type = $type;
        $this->encoding = $encoding;
        $this->id = $id;
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
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
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
