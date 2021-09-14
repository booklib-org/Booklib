# .mobi
Handle .mobi files with ease.

## Introduction
This package was born out of frustration. There didn't seem to be a
decent open source .mobi reader out there for PHP, so I wanted to 
create my own to give back to the community.

## Installation
```
composer require choccybiccy/mobi
```

## Usage
### Reading .mobi files
```
use Choccybiccy;

$mobi = new Mobi\Reader('sherlock.mobi');

echo $mobi->getTitle(); # The Adventures of Sherlock Holmes by Doyle
echo $mobi->getAuthor(); # Doyle, Arthur Conan, Sir, 1859-1930
```
#### .mobi headers
```
use Choccybiccy;

$mobi = new Mobi\Reader('sherlock.mobi');

$palmDb = $mobi->getPalmDbHeader();
$palmDoc = $mobi->getPalmDocHeader();
$mobi = $mobi->getMobiHeader();
$exth = $mobi->getExthHeader();
```

#### EXTH records
```
echo $exth->getRecordByType(Mobi\Header\ExthHeader::TYPE_AUTHOR); # Doyle, Arthur Conan, Sir, 1859-1930
echo $exth->getRecordByType(Mobi\Header\ExthHeader::TYPE_PUBLISHER); # Mobipocket (an Amazon.com company)
```

## Testing
```
./vendor/bin/phpunit
```

## Todo
- [x] Parse .mobi files
- [ ] More tests
- [x] EXTH record type constants
- [ ] Helper methods to get common attributes (title, author etc)

## Thanks
* http://wiki.mobileread.com/wiki/MOBI
