<?php
require_once '../vendors/simpletest/autorun.php';

class TestOfRegex extends UnitTestCase
{
    /**
     * Returns an array with words splitters from a text.
     *
     * I found this regex on the web, but i can not remember where.
     *
     * @param string $text
     * @return array
     */
    public function splitToWords($text)
    {
    	return preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $text, -1, PREG_SPLIT_NO_EMPTY);
    }

    public function testAlphanumericonly()
    {
        $items = array(
            '601 47 744.8' => '601477448',
            'EP 2 581 779' => 'EP2581779'
        );
        foreach ($items as $s=>$good) {
            $this->assertEqual(preg_replace("/[^a-zA-Z0-9]+/", "", $s), $good);
        }
    }
    
    public function testSplitToWords()
    {
        $items = array(
            'Am Anfang war das Wort' => array('Am', 'Anfang', 'war', 'das', 'Wort'),
            'EP 2 581 779' => array('EP', '2', '581', '779')
        );
        foreach ($items as $s=>$good) {
            $this->assertEqual($this->splitToWords($s), $good);
        }
    }
}
?>