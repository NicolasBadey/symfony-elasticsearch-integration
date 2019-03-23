<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\tests\Model\ETL\Article;

use App\Model\ETL\Article\ArticleLoad;
use App\Model\Search\DTO\ArticleSearch;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SearchToArticleQueryTransformerTest extends KernelTestCase
{
    public function testTransformArticles()
    {
        $search = new ArticleSearch();

        $search->search = 'lorem';
        $search->latitude = 42;
        $search->longitude = 44;
        $search->distance = 20;

        self::bootKernel();
        $container = self::$container;

        $query = $container->get('App\Model\Search\Transformer\SearchToArticleQueryTransformer')->transform($search);

        $this->assertSame([
            'index' => ArticleLoad::getAlias(),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'multi_match' => [
                                    'query' => 'lorem',
                                    'fields' => [
                                        'title^3',
                                        'content',
                                    ],
                                    'minimum_should_match' => '50%',
                                    'type' => 'most_fields',
                                    'fuzziness' => 'AUTO',
                                ],
                            ],
                        ],
                        'filter' => [
                            [
                                'geo_distance' => [
                                    'distance' => 20,
                                    'pin.location' => [
                                        'lat' => 42,
                                        'lon' => 44,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $query);
    }
}
