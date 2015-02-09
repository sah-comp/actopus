<?php
/**
 * Cinnebar.
 *
 * My lightweight no-framework framework written in PHP.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */

/**
 * Calculates laptimes between benchmarks using microtime.
 *
 * @package Cinnebar
 * @subpackage Stopwatch
 * @version $Id$
 */
class Cinnebar_Stopwatch
{
    /**
     * Holds the marks of our stopwatch.
     *
     * @var array
     */
    public $marks = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Returns the value of a mark.
     *
     * @return string
     */
    public function __get($mark)
    {
        if ( ! isset($this->marks[$mark])) return null;
        return $this->marks[$mark];
    }
    
    /**
     * Sets the start mark.
     *
     * @return Cinnebar_Stopwatch $this
     */
    public function start()
    {
        $this->marks['start'] = microtime(true);
        return $this;
    }
    
    /**
     * Set a benchmark.
     *
     * @param string $mark
     * @return Cinnebar_Stopwatch $this
     */
    public function mark($mark)
    {
        $this->marks[$mark] = microtime(true);
        return $this;
    }
    
    /**
     * Calculates the time between two marks and returns the result.
     *
     * @param mixed $mark1
     * @param mixed $mark2
     * @param int $digits
     * @return float $diffBetweenMark1AndMark2
     */
    public function laptime($mark1 = 'start', $mark2 = 'stop', $digits = 5)
    {
        if ( ! isset($this->marks[$mark1]) || ! isset($this->marks[$mark2])) return 0.0000;
        return round($this->marks[$mark2] - $this->marks[$mark1], $digits);
    }
}
