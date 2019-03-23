<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\ETL\Article;

use ElasticsearchETL\AbstractTransform;

class ArticleTransform extends AbstractTransform
{
    public function transformObject($article): array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'category' => $article->getCategory() ? $article->getCategory()->getName() : null,
            'location' => [
                'lat' => $article->getLatitude(),
                'lon' => $article->getLongitude(),
            ],
        ];
    }
}
