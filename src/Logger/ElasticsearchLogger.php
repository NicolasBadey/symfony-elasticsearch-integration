<?php

/*
* Simplified version of ElasticaBundle Logger
 */

namespace App\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Class ElasticsearchLogger.
 */
class ElasticsearchLogger extends AbstractLogger
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $queries = [];

    protected $queryLogs = [];

    /**
     * Constructor.
     *
     * @param LoggerInterface|null $logger The Symfony logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function logQuery(array $requestInfo)
    {
        //this is only perform for DataCollector purpose, so only if it's Debug mode only
        if (getenv('APP_DEBUG')) {
            $data = $requestInfo['request']['body'];
            $e = new \Exception();
            if (\is_string($data)) {
                $jsonStrings = explode("\n", $data);
                $data = [];
                foreach ($jsonStrings as $json) {
                    if ('' !== $json) {
                        $data[] = json_decode($json, true);
                    }
                }
            } else {
                $data = [$data];
            }

            $result = json_decode($requestInfo['response']['body'], true);
            $this->queries[] = [
                'url' => $requestInfo['response']['transfer_stats']['url'],
                'scheme' => $requestInfo['request']['scheme'],
                'method' => $requestInfo['request']['http_method'],
                'data' => $data,
                'status' => $requestInfo['response']['status'],
                'executionMS' => $requestInfo['response']['transfer_stats']['total_time'] * 1000,
                'queryString' => 'toto',
                'itemCount' => $result['hits']['total'] ?? '',
                'backtrace' => $e->getTraceAsString(),
            ];
        }
    }

    /**
     * Returns the number of queries that have been logged.
     *
     * @return int The number of queries logged
     */
    public function getNbQueries()
    {
        return \count($this->queries);
    }

    /**
     * Returns a human-readable array of queries logged.
     *
     * @return array An array of queries
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $context);
    }

    public function reset()
    {
        $this->queries = [];
    }
}
