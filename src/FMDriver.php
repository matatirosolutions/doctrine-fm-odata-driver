<?php
declare(strict_types=1);

namespace MSDev\DoctrineFMODataDriver;

use Doctrine\DBAL\Connection as ParentConnection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use SensitiveParameter;
use Exception;
use Symfony\Component\HttpClient\HttpClient;

class FMDriver implements Driver
{

    public function connect(
        #[SensitiveParameter] array $params
    ): Driver\Connection
    {
        $client = HttpClient::createForBaseUri($this->baseURL($params), [
            'auth_basic' => [$params['user'], $params['password']],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);

        return new FMConnection($client);
    }

    public function getDatabasePlatform()
    {
        // TODO: Implement getDatabasePlatform() method.
        //throw new Exception('not implemented');
        return new FMPlatform();
    }

    public function getSchemaManager(ParentConnection $conn, AbstractPlatform $platform)
    {
        // TODO: Implement getSchemaManager() method.
        throw new Exception('not implemented');
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        // TODO: Implement getExceptionConverter() method.
        throw new Exception('not implemented');
    }

    private function baseURL(array $params): string
    {
        $server = str_starts_with($params['host'], 'http') ? $params['host'] : "https://{$params['host']}";
        return (str_ends_with($server, '/') ? $server : $server . '/') . 'fmi/odata/v4/' . $params['dbname'] . '/';
    }

}
