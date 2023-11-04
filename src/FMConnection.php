<?php
declare(strict_types=1);

namespace MSDev\DoctrineFMODataDriver;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 * @method object getNativeConnection()
 */
class FMConnection implements Connection
{

    public function __construct(
        private readonly HttpClientInterface $client
    ) {
    }


    /**
     * @inheritDoc
     */
    public function prepare(string $sql): Statement
    {
        // TODO: Implement prepare() method.
        throw new Exception('not implemented');
    }

    /**
     * @inheritDoc
     */
    public function query(string $sql): Result
    {
        // TODO: Implement query() method.
        throw new Exception('not implemented');
    }

    /**
     * @inheritDoc
     */
    public function quote($value, $type = ParameterType::STRING)
    {
        // TODO: Implement quote() method.
        throw new Exception('not implemented');
    }

    /**
     * @inheritDoc
     */
    public function exec(string $sql): int
    {
        // TODO: Implement exec() method.
        throw new Exception('not implemented');
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId($name = null)
    {
        // TODO: Implement lastInsertId() method.
        throw new Exception('not implemented');
    }

    /**
     * @inheritDoc
     */
    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
        throw new Exception('not implemented');
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        // TODO: Implement commit() method.
        throw new Exception('not implemented');
    }

    /**
     * @inheritDoc
     */
    public function rollBack()
    {
        // TODO: Implement rollBack() method.
        throw new Exception('not implemented');
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method object getNativeConnection()
        throw new Exception('not implemented');
    }
}
