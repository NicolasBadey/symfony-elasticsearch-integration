<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\ETL\Article;

use ElasticsearchETL\AbstractETLBuilder;

class ArticleETLBuilder extends AbstractETLBuilder
{
    /**
     * ETLBuilder constructor.
     */
    public function __construct(ArticleLoad $load, ArticleExtract $extract, ArticleTransform $transform)
    {
        $this->load = $load;
        $this->extract = $extract;
        $this->transform = $transform;
    }
}
