<?php

/**
 * ~~summary~~
 * 
 * ~~description~~
 * 
 * PHP version 5
 * 
 * @category  Net
 * @package   PEAR2_Net_Transmitter
 * @author    Vasil Rangelov <boen.robot@gmail.com>
 * @copyright 2011 Vasil Rangelov
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version   GIT: $Id$
 * @link      http://pear2.php.net/PEAR2_Net_Transmitter
 */
/**
 * The namespace declaration.
 */
namespace PEAR2\Net\Transmitter;

/**
 * A filter collection.
 * 
 * Represents a collection of stream filters.
 * 
 * @category Net
 * @package  PEAR2_Net_Transmitter
 * @author   Vasil Rangelov <boen.robot@gmail.com>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link     http://pear2.php.net/PEAR2_Net_Transmitter
 * @see      Client
 */
class FilterCollection implements \SeekableIterator, \Countable
{
    /**
     * @var array The filter collection itself.
     */
    protected $filters = array();
    
    /**
     * @var int A pointer, as required by SeekableIterator.
     */
    protected $position = 0;
    
    /**
     * Appends a filter to the collection
     * 
     * @param string $name   The name of the filter.
     * @param array  $params An array of parameters for the filter.
     * 
     * @return $this The collection itself.
     */
    public function append($name, array $params = array())
    {
        $this->filters[] = array((string) $name, $params);
        return $this;
    }
    
    /**
     * Inserts the filter before a position.
     * 
     * Inserts the specified filter before a filter at a specified position. The
     * new filter takes the specified position, while previous filters are moved
     * forward by one.
     * 
     * @param int    $position The position before which the filter will be
     *     inserted.
     * @param string $name     The name of the filter.
     * @param array  $params   An array of parameters for the filter.
     * 
     * @return $this The collection itself.
     */
    public function insertBefore($position, $name, array $params = array())
    {
        $position = (int) $position;
        if ($position <= 0) {
            $this->filters = array_merge(
                array(0 => array((string) $name, $params)),
                $this->filters
            );
            return $this;
        }
        if ($position > count($this->filters)) {
            return $this->append($name, $params);
        }
        $this->filters = array_merge(
            array_slice($this->filters, 0, $position),
            array(0 => array((string) $name, $params)),
            array_slice($this->filters, $position)
        );
        return $this;
    }
    
    /**
     * Removes a filter at a specified position.
     * 
     * @param int $position The position from which to remove a filter.
     * 
     * @return $this The collection itself.
     */
    public function removeAt($position)
    {
        unset($this->filters[$position]);
        $this->filters = array_values($this->filters);
        return $this;
    }
    
    /**
     * Clears the collection
     * 
     * @return $this The collection itself.
     */
    public function clear()
    {
        $this->filters = array();
        return $this;
    }

    /**
     * Gets the number of filters in the collection.
     * 
     * @return int The number of filters in the collection.
     */
    public function count()
    {
        return count($this->filters);
    }

    /**
     * Resets the pointer to 0.
     * 
     * @return bool TRUE if the collection is not empty, FALSE otherwise.
     */
    public function rewind()
    {
        return $this->seek(0);
    }

    /**
     * Moves the pointer to a specified position.
     * 
     * @param int $position The position to move to.
     * 
     * @return bool TRUE if the specified position is valid, FALSE otherwise.
     */
    public function seek($position)
    {
        $this->position = $position;
        return $this->valid();
    }
    
    /**
     * Gets the current position.
     * 
     * @return int The current position.
     */
    public function getCurrentPosition()
    {
        return $this->position;
    }

    /**
     * Moves the pointer forward by 1.
     * 
     * @return bool TRUE if the new position is valid, FALSE otherwise.
     */
    public function next()
    {
        ++$this->position;
        return $this->valid();
    }

    /**
     * Gets the filter name at the current pointer position.
     * 
     * @return string|false The name of the filter at the current position.
     */
    public function key()
    {
        return $this->valid() ? $this->filters[$this->position][0] : false;
    }

    /**
     * Gets the filter parameters at the current pointer position.
     * 
     * @return array|false An array of parameters for the filter at the current
     *     position, or FALSE if the position is not valid.
     */
    public function current()
    {
        return $this->valid() ? $this->filters[$this->position][1] : false;
    }

    /**
     * Moves the pointer backwards by 1.
     * 
     * @return bool TRUE if the new position is valid, FALSE otherwise.
     */
    public function prev()
    {
        --$this->position;
        return $this->valid();
    }

    /**
     * Moves the pointer to the last valid position.
     * 
     * @return bool TRUE if the collection is not empty, FALSE otherwise.
     */
    public function end()
    {
        $this->position = count($this->filters) - 1;
        return $this->valid();
    }

    /**
     * Checks if the pointer is still pointing to an existing offset.
     * 
     * @return bool TRUE if the pointer is valid, FALSE otherwise.
     */
    public function valid()
    {
        return array_key_exists($this->position, $this->filters);
    }
}
