<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Search\DTO;

/**
 * in DTO property are public.
 */
class ArticleSearch
{
    /**
     * @var string
     */
    public $search;

    /**
     * @var float
     */
    public $longitude;

    /**
     * @var float
     */
    public $latitude;

    /**
     * @var float
     */
    public $distance;
}
