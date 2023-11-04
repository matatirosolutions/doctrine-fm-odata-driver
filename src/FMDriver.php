<?php
declare(strict_types=1);

namespace MSDev\DoctrineFMODataDriver;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use SensitiveParameter;
use Exception;
use Symfony\Component\HttpClient\HttpClient;

class FMDriver implements Driver
{

    /**
     * @inheritDoc
     */
    public function connect(
        #[SensitiveParameter] array $params
    ) {
        $client = HttpClient::create();

        return new FMConnection($client);
    }

    /**
     * @inheritDoc
     */
    public function getDatabasePlatform()
    {
        // TODO: Implement getDatabasePlatform() method.
        throw new Exception('not implemented');
    }

    /**
     * @inheritDoc
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        // TODO: Implement getSchemaManager() method.
        throw new Exception('not implemented');
    }

    /**
     * @inheritDoc
     */
    public function getExceptionConverter(): ExceptionConverter
    {
        // TODO: Implement getExceptionConverter() method.
        throw new Exception('not implemented');
    }

}
