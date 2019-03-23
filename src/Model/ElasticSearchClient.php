<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

use App\Logger\ElasticsearchLogger;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Namespaces\IndicesNamespace;
use ElasticsearchETL\ElasticsearchClientInterface;

/**
 * Class ElasticSearchClient.
 */
class ElasticSearchClient implements ElasticsearchClientInterface
{
    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @var ElasticsearchLogger
     */
    private $logger;

    /**
     * ElasticSearchClient constructor.
     */
    public function __construct(array $elasticsearch_config, ElasticsearchLogger $logger)
    {
        $this->logger = $logger;
        $this->client = ClientBuilder::create()
            ->setHosts($elasticsearch_config['hosts'])
            ->setLogger($logger)
            ->build();
    }

    public function index(array $params): array
    {
        $data = $this->client->index($params);
        $this->logRequestInfo();

        return $data;
    }

    public function delete(array $params): array
    {
        $data = $this->client->delete($params);
        $this->logRequestInfo();

        return $data;
    }

    public function bulk(array $params = []): array
    {
        $data = $this->client->bulk($params);
        $this->logRequestInfo();

        return $data;
    }

    /**
     * remove array that contains [].
     */
    public function cleanArray(&$array)
    {
        foreach ($array as $key => $item) {
            if (\is_array($item)) {
                $array[$key] = $this->cleanArray($item);
            }

            if (empty($array[$key])) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    public function search(array $params): array
    {
        $data = $this->client->search($this->cleanArray($params));
        $this->logRequestInfo();

        return $data;
    }

    public function suggest(array $params): array
    {
        $data = $this->client->suggest($params);
        $this->logRequestInfo();

        return $data;
    }

    /**
     * @return \Elasticsearch\Namespaces\ClusterNamespace
     */
    public function cluster()
    {
        $data = $this->client->cluster();
        $this->logRequestInfo();

        return $data;
    }

    private function logRequestInfo()
    {
        $this->logger->logQuery($this->client->transport->getConnection()->getLastRequestInfo());
    }

    /**
     * @return array|bool
     */
    public function exists(array $params)
    {
        return $this->client->exists($params);
    }

    /**
     * @return array
     */
    public function count(array $params)
    {
        return $this->client->count($params);
    }

    /**
     * @return array
     */
    public function refresh(array $params)
    {
        return $this->client->indices()->refresh($params);
    }

    public function indices(): IndicesNamespace
    {
        return $this->client->indices();
    }

    public function getIndexNameFromAlias(string $alias): array
    {
        $aliaseInfo = $this->client->indices()->getAlias([
            'name' => $alias,
        ]);

        return array_keys($aliaseInfo);
    }
}
