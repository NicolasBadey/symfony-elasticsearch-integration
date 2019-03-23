<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Search\Transformer;

use App\Model\ETL\Article\ArticleLoad;
use App\Model\Search\DTO\ArticleSearch;

class SearchToArticleQueryTransformer
{
    public function transform(ArticleSearch $search): array
    {
        /*
         * https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html
         * https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html
         */
        return [
            'index' => ArticleLoad::getAlias(),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            $this->getSearchTextQuery($search),
                            //....
                        ],
                        'filter' => [
                            $this->getGeoDistanceQuery($search),
                            //....
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getGeoDistanceQuery(ArticleSearch $search): array
    {
        if ($search->latitude > 0 && $search->longitude > 0 && $search->distance > 0) {
            return [
                'geo_distance' => [
                    'distance' => $search->distance,
                    'pin.location' => [
                        'lat' => $search->latitude,
                        'lon' => $search->longitude,
                    ],
                ],
            ];
        }

        return [];
    }

    protected function getSearchTextQuery(ArticleSearch $search): array
    {
        if (mb_strlen($search->search) > 0) {
            return [
                'multi_match' => [
                    'query' => $search->search,
                    'fields' => [
                        'title^3',
                        'content',
                    ],
                    'minimum_should_match' => '50%',
                    'type' => 'most_fields',
                    'fuzziness' => 'AUTO',
                    //'operator' => 'and', //look at cross_fields type before use operator "and"
                ],
            ];
        }

        return [
                'match_all' => (object) [],
             ];
    }
}
