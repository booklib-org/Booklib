<?php

namespace Choccybiccy\Mobi\Header;

use Choccybiccy\Mobi\Exception\NoSuchRecordException;
use Choccybiccy\Mobi\Header\Record\ExthRecord;

/**
 * Class ExthHeader.
 *
 * @method ExthRecord[] getIterator()
 */
class ExthHeader extends AbstractRecordHeader
{
    const TYPE_AUTHOR = 100;

    const TYPE_PUBLISHER = 101;

    const TYPE_IMPRINT = 102;

    const TYPE_DESCRIPTION = 103;

    const TYPE_ISBN = 104;

    const TYPE_SUBJECT = 105;

    const TYPE_PUBLISHING_DATE = 106;

    const TYPE_REVIEW = 107;

    const TYPE_CONTRIBUTOR = 108;

    const TYPE_RIGHTS = 109;

    const TYPE_SUBJECT_CODE = 110;

    const TYPE_TYPE = 111;

    const TYPE_SOURCE = 112;

    const TYPE_ASIN = 113;

    const TYPE_VERSION_NUMBER = 114;

    const TYPE_SAMPLE = 115;

    const TYPE_START_READING = 116;

    const TYPE_ADULT = 117;

    const TYPE_RETAIL_PRICE = 118;

    const TYPE_RETAIL_PRICE_CURRENCY = 119;

    const TYPE_KF8_BOUNDARY_OFFSET = 121;

    const TYPE_RESOURCE_COUNT = 125;

    const TYPE_KF8_COVER_URI = 129;

    const TYPE_DICTIONARY_SHORT_NAME = 200;

    const TYPE_COVER_OFFSET = 201;

    const TYPE_THUMB_OFFSET = 202;

    const TYPE_HAS_FAKE_COVER = 203;

    const TYPE_CREATOR_SOFTWARE = 204;

    const TYPE_CREATOR_MAJOR_VERSION = 205;

    const TYPE_CREATOR_MINOR_VERSION = 206;

    const TYPE_CREATOR_BUILD_NUMBER = 207;

    const TYPE_TYPE_WATERMARK = 208;

    const TYPE_TAMPER_PROOF_KEYS = 209;

    const TYPE_CDE_TYPE = 501;

    const TYPE_LAST_UPDATE_TIME = 502;

    const TYPE_UPDATED_TITLE = 503;

    const TYPE_ASIN_COPY = 504;

    const TYPE_LANGUAGE = 524;

    const TYPE_ALIGNMENT = 525;

    const TYPE_CREATOR_BUILD_NUMBER_KINGLEGEN_2_7 = 535;

    const TYPE_IN_MEMORY = 547;

    /**
     * @var int
     */
    protected $length;

    /**
     * ExthHeader constructor.
     *
     * @param int   $length
     * @param array $records
     */
    public function __construct($length, array $records = [])
    {
        parent::__construct($records);
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $type
     *
     * @return mixed
     *
     * @throws NoSuchRecordException
     */
    public function getRecordByType($type)
    {
        $iterator = $this->getIterator();
        foreach ($iterator as $record) {
            if ($type == $record->getType()) {
                return $record->getData();
            }
        }
        throw new NoSuchRecordException('No such EXTH record matching type '.$type.' found');
    }
}
