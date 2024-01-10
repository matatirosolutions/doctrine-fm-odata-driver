<?php

namespace MSDev\DoctrineFMODataDriver;

class FMQuery
{
    private array $parameters = [];

    private string $layout;

    private string $method;

    private string $queryString;

    private string $queryBody;

    private string $operation;

    public function __construct(
        private string $sqlQuery
    ) {
    }

    public function getSqlQuery(): string
    {
        return $this->sqlQuery;
    }

    public function setSqlQuery(string $sqlQuery): FMQuery
    {
        $this->sqlQuery = $sqlQuery;
        return $this;
    }

    public function saveParameter(int $param,int|string $value)
    {
        $this->parameters[$param] = $value;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): FMQuery
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): FMQuery
    {
        $this->layout = $layout;
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): FMQuery
    {
        $this->method = $method;
        return $this;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function setQueryString(string $queryString): FMQuery
    {
        $this->queryString = $queryString;
        return $this;
    }

    public function getQueryBody(): string
    {
        return $this->queryBody;
    }

    public function setQueryBody(string $queryBody): FMQuery
    {
        $this->queryBody = $queryBody;
        return $this;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): FMQuery
    {
        $this->operation = $operation;
        return $this;
    }

}
