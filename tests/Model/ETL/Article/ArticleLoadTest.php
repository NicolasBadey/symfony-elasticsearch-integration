<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\tests\Model\ETL\Article;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticleLoadTest extends KernelTestCase
{
    public function testGetAlias()
    {
        self::bootKernel();
        $container = self::$container;

        $alias = $container->get('App\Model\ETL\Article\ArticleLoad')->getAlias();

        $this->assertSame('article_test', $alias);
    }

    public function testGetMappingProperties()
    {
        self::bootKernel();
        $container = self::$container;

        $mapping = $container->get('App\Model\ETL\Article\ArticleLoad')->getMappingProperties();

        $this->assertSame([
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
        ], $mapping);
    }
}
