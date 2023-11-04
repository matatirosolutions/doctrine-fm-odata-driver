<?php
declare(strict_types=1);

namespace MSDev\DoctrineFMODataDriver;

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;

class FMStatement implements Statement
{

    /**
     * @inheritDoc
     */
    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        // TODO: Implement bindValue() method.
    }

    /**
     * @inheritDoc
     */
    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
    {
        // TODO: Implement bindParam() method.
    }

    /**
     * @inheritDoc
     */
    public function execute($params = null): Result
    {
        // TODO: Implement execute() method.
    }
}
