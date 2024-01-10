<?php

namespace MSDev\DoctrineFMODataDriver;

use MSDev\DoctrineFMODataDriver\Exception\FMException;
use PHPSQLParser\PHPSQLParser;
use RuntimeException;

class MySQLToOData
{
    private readonly PHPSQLParser $sqlParser;

    private array $phpQuery;

    public function __construct()
    {
        $this->sqlParser = new PHPSQLParser();
    }

    /**
     * @throws FMException
     */
    public function prepareQuery(FMQuery $query): void
    {
        $this->phpQuery = $this->sqlParser->parse($query->getSqlQuery());
        $query->setOperation(strtolower(array_keys($this->phpQuery)[0]));
        $this->setLayout($query);

        switch($query->getOperation()) {
            case 'select':
                $query->setMethod('GET');
                $this->generateFindCommand($query);
                return;
//            case 'update':
//                return $this->generateUpdateCommand($phpQuery, $statement, $params);
//            case 'insert':
//                return $this->generateInsertCommand($phpQuery, $params);
//            case 'delete':
//                return $this->generateDeleteCommand($phpQuery, $params);
        }

        throw new RuntimeException('Unknown request type');

        dd($query);
        //dd($phpQuery);
    }

    public function prepareResult(array $results): array
    {
        $fields = $this->phpQuery['SELECT'];
        if ('subquery' === $this->phpQuery['FROM'][0]['expr_type']) {
            $fields = $this->phpQuery['FROM'][0]['sub_tree']['FROM'][0]['sub_tree']['SELECT'];
        }
//dump($fields);
//dd($results);
        $records = [];
        foreach($results as $result) {
            $record = [];
            foreach ($fields as $field) {
                $data = $result[$field['no_quotes']['parts'][1]];
                $record[$field['alias']['no_quotes']['parts'][0]] = $data === '' ? null : $data;
            }
            $records[] = $record;
        }
//dd($records);
        return $records;
    }

    /**
     * @throws FMException
     */
    private function generateFindCommand(FMQuery $query): void
    {
        // What columns do we want
        $queryString = '$select=' . implode(',', $this->selectColumns());

        // Do we need to apply a filter
        if(array_key_exists('WHERE', $this->phpQuery)) {
            $queryString .= '&$filter=' . $this->generateWhere($query);
        }

        // Sort order
        if(array_key_exists('ORDER', $this->phpQuery)) {
            $orderArray = array_map(static function($orderBy) {
                return $orderBy['no_quotes']['parts'][1] . ' ' . strtolower($orderBy['direction']) ;
            }, $this->phpQuery['ORDER']);
            $queryString .= '&$orderby=' . implode(',', $orderArray);
        }

        // What about a limit or offset
        if(array_key_exists('LIMIT', $this->phpQuery)) {
            if(array_key_exists('offset', $this->phpQuery['LIMIT'])) {
                $queryString .= '&$skip=' . $this->phpQuery['LIMIT']['offset'];
            }

            if(array_key_exists('rowcount', $this->phpQuery['LIMIT'])) {
                $queryString .= '&$top=' . $this->phpQuery['LIMIT']['rowcount'];
            }
        }

        $query->setQueryString($queryString);
    }

    /**
     * @throws FMException
     */
    private function setLayout(FMQuery $query): void
    {
        if (empty($this->phpQuery['FROM']) && empty($this->phpQuery['INSERT']) && empty($this->phpQuery['UPDATE'])) {
            throw new FMException('Unknown layout');
        }

        switch($query->getOperation()) {
            case 'insert':
                $query->setLayout($this->phpQuery['INSERT'][1]['no_quotes']['parts'][0]);
                return;
            case 'update':
                $query->setLayout($this->phpQuery['UPDATE'][0]['no_quotes']['parts'][0]);
                return;
            default:
                if('subquery' == $this->phpQuery['FROM'][0]['expr_type']) {
                    $this->phpQuery = $this->phpQuery['FROM'][0]['sub_tree']['FROM'][0]['sub_tree'];
                    $query->setLayout($this->phpQuery['FROM'][0]['sub_tree']['FROM'][0]['sub_tree']['FROM'][0]['no_quotes']['parts'][0]);
                    return;
                }
                $query->setLayout($this->phpQuery['FROM'][0]['no_quotes']['parts'][0]);
        }
    }

    /**
     * @throws FMException
     */
    private function generateWhere(FMQuery $fmQuery): string
    {
        $request = [];
        $requests = [];
        $cols = $this->selectColumns();
        $pc = 1;

        foreach ($this->phpQuery['WHERE'] as $c => $cValue) {
            $query = $cValue;

            if(array_key_exists($query['base_expr'], $cols)) {
                $op = $this->getOperator($this->phpQuery['WHERE'][$c+1]['base_expr'], $fmQuery->getParameters()[$pc]);
                if(' ne ' === $op) {
                    // if this isn't the first loop, add the current request to the requests array and reset it
                    if(!empty($request)) {
                        $requests[] = $request;
                        $request = [];
                    }
                    $requests[] = [
                        $query['no_quotes']['parts'][1] => ($fmQuery->getParameters()[$pc] === false ? 0 : $fmQuery->getParameters()[$pc]),
                        'omit' => "true",
                    ];

                } elseif('IN' === $op) {
                    $baseRequest = $request;
                    $inCount = substr_count($this->phpQuery['WHERE'][$c+2]['base_expr'], '?');
                    for($i = 0; $i < $inCount; $i++) {
                        $request = $baseRequest;
                        $request[$query['no_quotes']['parts'][1]] = '==' . ($fmQuery->getParameters()[$pc] === false ? 0 : $fmQuery->getParameters()[$pc]);
                        $requests[] = $request;
                        $pc++;
                    }
                    $request = null;
                } else {
                    $request[] = $query['no_quotes']['parts'][1] . $op . ($fmQuery->getParameters()[$pc] === false ? 0 : $fmQuery->getParameters()[$pc]);
                }
                $pc++;
            } elseif ('bracket_expression' === $query['expr_type']) {
                $baseRequest = $request;
                foreach($query['sub_tree'] as $subCount => $subExpression) {
                    if(isset($subExpression['no_quotes']['parts'][1])
                        && 'colref' === $subExpression['expr_type']
                        && array_key_exists('no_quotes', $subExpression)
                        && array_key_exists($subExpression['base_expr'], $cols)
                    ) {
                        $op = $this->getOperator($query['sub_tree'][$subCount+1]['base_expr'], $fmQuery->getParameters()[$pc]);
                        $request = $baseRequest;
                        $request[] = $subExpression['no_quotes']['parts'][1] . $op . ($fmQuery->getParameters()[$pc] === false ? 0 : $fmQuery->getParameters()[$pc]);
                        $requests[] = $request;

                        $request = [];
                        $pc++;
                    }
                }
            }
        }

        if(!empty($request)) {
            $requests[] = $request;
        }

        $requestString = '';
        $requestCount = count($requests);

        foreach ($requests as $requestCounter => $request) {
            if($requestCount > 1) {
                if($requestCounter > 1) {
                    $requestString .= ' or ';
                }
                $requestString .= '(';
            }
            $requestString .= implode(' and ', $request);
            if($requestCount > 1) {
                $requestString .= ')';
            }
        }
        return $requestString;
    }

    private function selectColumns(): array
    {
        $columns = [];
        foreach($this->phpQuery['SELECT'] as $column) {
            if(isset($column['no_quotes'])) {
                $columns[$column['base_expr']] = $column['no_quotes']['parts'][1];
                continue;
            }

            if(isset($column['sub_tree'])) {
                $field = [];
                foreach($column['sub_tree'] as $sub) {
                    $field[] = end($sub['no_quotes']['parts']);
                }
                $columns[$column['base_expr']] = implode(' ', $field);
            }
        }

        return $columns;
    }

    private function getOperator($request, $parameter)
    {
        switch ($request) {
            case '=':
                $param = substr($parameter, 0, 1);
                if (in_array($param, ['=', '<', '>'])) {
                    return '';
                }
                return ' eq ';
            case '>':
                return ' gt ';
            case '<':
                return ' lt ';
            case '>=':
            case '=<':
                return ' ge';
            case '<=':
            case '=>':
                return ' le ';
            case 'IN':
                return $request;
            case '<>':
            case '!=':
                return ' ne ';
            case 'LIKE':
                return ' has ';
            default:
                throw new FMException('Unknown operator '. $request);
        }
    }
}
