<?php

declare(strict_types=1);

/**
 * class DbalResult
 *
 * Buffered result of a query executed by DbalAdapter. Buffering makes the
 * record count reliable on every driver (PDO reports 0 rows for SELECT on SQLite).
 *
 * @package FormHandler
 */
class DbalResult
{
    private $_rows;
    private $_position = 0;

    /**
     * DbalResult::__construct()
     *
     * @param array $rows the fetched records in assoc mode
     */
    public function __construct(array $rows)
    {
        $this->_rows = $rows;
    }

    /**
     * DbalResult::fetch()
     *
     * @return array|false the next record or false when there are no records left
     */
    public function fetch()
    {
        if ($this->_position >= count($this->_rows))
        {
            return false;
        }

        return $this->_rows[$this->_position++];
    }

    /**
     * DbalResult::count()
     *
     * @return int the number of records
     */
    public function count()
    {
        return count($this->_rows);
    }
}
