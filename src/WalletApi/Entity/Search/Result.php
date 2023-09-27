<?php

namespace Paysera\WalletApi\Entity\Search;

/**
 * Base search result
 */
class Result implements \IteratorAggregate
{
    /**
     * @var integer
     */
    protected $total;

    /**
     * @var integer
     */
    protected $offset;

    /**
     * @var integer
     */
    protected $limit;

    public function __construct(protected array $resultList)
    {
    }

    /**
     * Gets limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Gets offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Gets total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getResultList());
    }

    /**
     * @return array
     */
    public function getResultList()
    {
        return $this->resultList;
    }
}
