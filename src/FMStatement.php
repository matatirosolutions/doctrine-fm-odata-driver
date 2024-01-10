<?php
declare(strict_types=1);

namespace MSDev\DoctrineFMODataDriver;

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class FMStatement implements Statement
{
    private readonly MySQLToOData $parser;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly FMQuery $query,
    ) {
        $this->parser = new MySQLToOData();
    }


    /**
     * @inheritDoc
     */
    public function bindValue($param, $value, $type = ParameterType::STRING): bool
    {
        $this->query->saveParameter($param, $value);
        dump($this->query);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
    {
        // TODO: Implement bindParam() method.
        dd('bindParam');
    }

    /**
     * @inheritDoc
     */
    public function execute($params = null): Result
    {
        try {
            $this->parser->prepareQuery($this->query);

            $result = $this->client->request(
                $this->query->getMethod(),
                $this->query->getLayout() . '?' . $this->query->getQueryString()
            );
            $body = json_decode($result->getContent(), true);
            $result = $this->parser->prepareResult($body['value']);

            return new FMResult($result);
        } catch (Throwable $except) {
            dd($except);
        }
    }
}
