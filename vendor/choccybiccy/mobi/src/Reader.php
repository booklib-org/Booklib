<?php

namespace Choccybiccy\Mobi;

use Choccybiccy\Mobi\Exception\InvalidFormatException;
use Choccybiccy\Mobi\Header\ExthHeader;
use Choccybiccy\Mobi\Header\MobiHeader;
use Choccybiccy\Mobi\Header\PalmDbHeader;
use Choccybiccy\Mobi\Header\PalmDocHeader;
use Choccybiccy\Mobi\Header\Record\ExthRecord;
use Choccybiccy\Mobi\Header\Record\PalmRecord;

/**
 * Class Reader.
 */
class Reader
{
    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var PalmDbHeader
     */
    protected $palmDbHeader;

    /**
     * @var int
     */
    protected $palmDbHeaderStart = 76;

    /**
     * @var PalmDocHeader
     */
    protected $palmDocHeader;

    /**
     * @var int
     */
    protected $palmDocHeaderStart;

    /**
     * @var MobiHeader
     */
    protected $mobiHeader;

    /**
     * @var int
     */
    protected $mobiHeaderStart;

    /**
     * @var ExthHeader
     */
    protected $exthHeader;

    /**
     * Reader constructor.
     *
     * @param string|\SplFileObject $file
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($file)
    {
        if (is_string($file)) {
            $file = new \SplFileObject($file, 'rb');
        }
        if (!($file instanceof \SplFileObject)) {
            throw new \InvalidArgumentException('File should either be a string or instance of \SplFileObject');
        }
        $this->file = $file;
        $this->parse();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        try {
            return $this->exthHeader->getRecordByType(ExthHeader::TYPE_UPDATED_TITLE);
        } catch (\Exception $e) {
            return $this->title;
        }
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        try {
            return $this->exthHeader->getRecordByType(ExthHeader::TYPE_AUTHOR);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getPublisher()
    {
        try {
            return $this->exthHeader->getRecordByType(ExthHeader::TYPE_PUBLISHER);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getIsbn()
    {
        try {
            return $this->exthHeader->getRecordByType(ExthHeader::TYPE_ISBN);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getContributor()
    {
        try {
            return $this->exthHeader->getRecordByType(ExthHeader::TYPE_CONTRIBUTOR);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getAsin()
    {
        try {
            return $this->exthHeader->getRecordByType(ExthHeader::TYPE_ASIN);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return PalmDbHeader
     */
    public function getPalmDbHeader()
    {
        return $this->palmDbHeader;
    }

    /**
     * @return PalmDocHeader
     */
    public function getPalmDocHeader()
    {
        return $this->palmDocHeader;
    }

    /**
     * @return MobiHeader
     */
    public function getMobiHeader()
    {
        return $this->mobiHeader;
    }

    /**
     * @return ExthHeader
     */
    public function getExthHeader()
    {
        return $this->exthHeader;
    }

    /**
     * @return \SplFileObject
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Parse the file data.
     */
    protected function parse()
    {
        $this->checkFormat();
        $this->parsePalmDb();
        $this->parsePalmDoc();
        $this->parseMobiHeader();
        $this->parseExth();
    }

    /**
     * @throws InvalidFormatException
     */
    protected function checkFormat()
    {
        $file = $this->file;
        $file->fseek(60);
        $content = $file->fread(8);
        if ($content !== 'BOOKMOBI') {
            throw new InvalidFormatException('The file is not a valid mobi file');
        }
    }

    /**
     * Parse the PalmDb records from the file.
     */
    protected function parsePalmDb()
    {
        $file = $this->file;
        $file->fseek($this->palmDbHeaderStart);
        $content = $file->fread(2);
        $records = hexdec(bin2hex($content));

        $this->palmDbHeader = new PalmDbHeader();
        for ($i = 0; $i < $records; ++$i) {
            $this->palmDbHeader->addRecord(new PalmRecord(
                $this->readData($file, 4),
                $this->readData($file, 1),
                $this->readData($file, 3)
            ));
        }
    }

    /**
     * Parse the PalmDoc header from the file.
     */
    protected function parsePalmDoc()
    {
        if (!$this->palmDbHeader) {
            return;
        }
        $file = $this->file;
        /** @var PalmRecord $firstPalmDbRecord */
        $firstPalmDbRecord = $this->palmDbHeader->getIterator()->offsetGet(0);
        $offset = $firstPalmDbRecord->getOffset();
        $this->palmDocHeaderStart = $offset;
        $file->fseek($offset);
        $this->palmDocHeader = new PalmDocHeader(
            $this->readData($file, 2),
            $this->readData($file, 4, $offset + 4),
            $this->readData($file, 2),
            $this->readData($file, 2),
            $this->readData($file, 2)
        );
    }

    /**
     * Parse the MOBI header from the file.
     */
    protected function parseMobiHeader()
    {
        if (!$this->palmDocHeader) {
            return;
        }
        $file = $this->file;
        $this->mobiHeaderStart = $file->ftell() + 2;
        $file->fseek($this->mobiHeaderStart);
        if ($file->fread(4) === 'MOBI') {
            $this->mobiHeader = new MobiHeader(
                $this->readData($file, 4),
                $this->readData($file, 4),
                $this->readData($file, 4),
                $this->readData($file, 4),
                $this->readData($file, 4)
            );
        }
        $file->fseek($this->mobiHeaderStart + 68);
        $data = $file->fread(8);
        $title = unpack('N*', $data);
        $file->fseek($this->mobiHeaderStart + ($title[1] - 16));
        $this->title = $file->fread($title[2]);
    }

    /**
     * Parse EXTH header from the file.
     */
    protected function parseExth()
    {
        if (!$this->mobiHeader) {
            return;
        }
        $file = $this->file;
        $file->fseek($this->mobiHeaderStart + $this->mobiHeader->getLength() + 4);
        $this->exthHeader = new ExthHeader($this->readData($file, 4));
        $records = $this->readData($file, 4);
        for ($i = 0; $i < $records; ++$i) {
            $type = $this->readData($file, 4);
            $length = $this->readData($file, 4);
            $data = $length > 0 ? $file->fread($length - 8) : '';
            $this->exthHeader->addRecord(new ExthRecord($type, $length, $data));
        }
    }

    /**
     * @param \SplFileObject $file
     * @param int            $length
     * @param int|null       $seek
     *
     * @return number
     */
    protected function readData(\SplFileObject &$file, $length, $seek = null)
    {
        if (is_int($seek)) {
            $file->fseek($seek);
        }

        return hexdec(bin2hex($file->fread($length)));
    }

    /**
     * @param \SplFileObject $file
     *
     * @return static
     */
    public function createFromFileObject(\SplFileObject $file)
    {
        return new static($file);
    }

    /**
     * Validate and return a string.
     *
     * @param $string
     *
     * @return string
     */
    public static function validateString($string)
    {
        if (is_string($string) || (is_object($string) && method_exists($string, '__toString'))) {
            return (string) $string;
        }
        throw new \InvalidArgumentException('Expected data must either be a string or stringable');
    }
}
