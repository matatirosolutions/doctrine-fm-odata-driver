<?php

namespace MSDev\DoctrineFMODataDriver;

use Doctrine\DBAL\Driver\Result;

class FMResult implements Result
{
    private int $rowCount = 0;

    public function __construct(
        private readonly array $results
    ) {
    }

    public function fetchNumeric()
    {
        // TODO: Implement fetchNumeric() method.
        dd(__METHOD__);
    }

    public function fetchAssociative(): array|false
    {
        if(array_key_exists($this->rowCount, $this->results)) {
            $row = $this->results[$this->rowCount];
            $this->rowCount++;
            return $row;
        }

        return false;
    }

    public function fetchOne()
    {
        // TODO: Implement fetchOne() method.
        dd(__METHOD__);
    }

    public function fetchAllNumeric(): array
    {
        // TODO: Implement fetchAllNumeric() method.
        dd(__METHOD__);
    }

    public function fetchAllAssociative(): array
    {
        // TODO: Implement fetchAllAssociative() method.
        dd(__METHOD__);
    }

    public function fetchFirstColumn(): array
    {
        // TODO: Implement fetchFirstColumn() method.
        dd(__METHOD__);
    }

    public function rowCount(): int
    {
        // TODO: Implement rowCount() method.
        dd(__METHOD__);
    }

    public function columnCount(): int
    {
        // TODO: Implement columnCount() method.
        dd(__METHOD__);
    }

    public function free(): void
    {
        // TODO: Implement free() method.
        //dd(__METHOD__);
        //return;
    }
}
