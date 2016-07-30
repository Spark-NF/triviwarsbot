<?php
namespace TriviWars\DB;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOException;

class ConnectionFromPDO extends Connection
{
    /** @var \PDO */
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        $this->_eventManager = new EventManager();
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($prepareString, $driverOptions = array())
    {
        try {
            return $this->pdo->prepare($prepareString, $driverOptions);
        } catch (\PDOException $exception) {
            throw new PDOException($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $args = func_get_args();
        $argsCount = count($args);

        try {
            if ($argsCount == 4) {
                return $this->pdo->query($args[0], $args[1], $args[2], $args[3]);
            }

            if ($argsCount == 3) {
                return $this->pdo->query($args[0], $args[1], $args[2]);
            }

            if ($argsCount == 2) {
                return $this->pdo->query($args[0], $args[1]);
            }

            return $this->pdo->query($args[0]);
        } catch (\PDOException $exception) {
            throw new PDOException($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function quote($input, $type = \PDO::PARAM_STR)
    {
        return $this->pdo->quote($input, $type);
    }

    /**
     * Executes an SQL statement and return the number of affected rows.
     *
     * @param string $statement
     *
     * @return integer
     */
    function exec($statement)
    {
        // TODO: Implement exec() method.
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null)
    {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * Initiates a transaction.
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commits a transaction.
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Rolls back the current transaction, as initiated by beginTransaction().
     *
     * @return boolean TRUE on success or FALSE on failure.
     */
    function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     * Returns the error code associated with the last operation on the database handle.
     *
     * @return string|null The error code, or null if no operation has been run on the database handle.
     */
    function errorCode()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Returns extended error information associated with the last operation on the database handle.
     *
     * @return array
     */
    function errorInfo()
    {
        return $this->pdo->beginTransaction();
    }
}