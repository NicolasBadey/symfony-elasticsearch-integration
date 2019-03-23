<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\ETL\Article;

use ElasticsearchETL\AbstractElasticsearchLoad;

class ArticleLoad extends AbstractElasticsearchLoad
{
    public static function getAlias(): string
    {
        return 'article_'.mb_strtolower(getenv('APP_ENV'));
    }

    public function getMappingProperties(): array
    {
        // if you are multi language use : https://www.elastic.co/guide/en/elasticsearch/guide/current/mixed-lang-fields.html

        return [
            'location' => [
                'type' => 'geo_point',
            ],
            'title' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
            'content' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
        ];
    }
}
