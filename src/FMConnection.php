<?php
declare(strict_types=1);

namespace MSDev\DoctrineFMODataDriver;

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 * @method object getNativeConnection()
 */
class FMConnection implements ServerInfoAwareConnection
{

    public function __construct(
        private readonly HttpClientInterface $client
    ) {
    }



    public function prepare(string $sql): Statement
    {
        return new FMStatement($this->client, new FMQuery($sql));
    }


    public function query(string $sql): Result
    {
        dd(__METHOD__);
    }


    public function quote($value, $type = ParameterType::STRING)
    {
        dd(__METHOD__);
    }


    public function exec(string $sql): int
    {
        dd(__METHOD__);
    }

    public function lastInsertId($name = null)
    {
        dd(__METHOD__);
    }

    public function beginTransaction()
    {
        dd(__METHOD__);
    }

    public function commit()
    {
        dd(__METHOD__);
    }

    public function rollBack()
    {
        dd(__METHOD__);
    }

    public function getServerVersion()
    {
        // TODO actually implement this
        return '2023';
    }

    public function __call($name, $arguments)
    {
        dd(__METHOD__);
    }

}
