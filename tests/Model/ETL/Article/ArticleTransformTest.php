<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\tests\Model\ETL\Article;

use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticleTransformTest extends KernelTestCase
{
    /**
     * @var Prophet
     */
    private $prophet;

    protected function setup(): void
    {
        $this->prophet = new Prophet();
    }

    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }

    public function testTransformArticles()
    {
        $article = $this->prophet->prophesize('App\Entity\Article');

        $article->getId()->willReturn(42);
        $article->getLongitude()->willReturn(42.24);
        $article->getLatitude()->willReturn(42.24);
        $article->getCategory()->willReturn(null);
        $article->getContent()->willReturn('lorem');
        $article->getTitle()->willReturn('title42');

        self::bootKernel();
        $container = self::$container;

        $articleArray = $container->get('App\Model\ETL\Article\ArticleTransform')->transformObjects([$article->reveal()]);

        $this->assertSame([
            [
                'id' => 42,
                'title' => 'title42',
                'content' => 'lorem',
                'category' => null,
                'location' => [
                    'lat' => 42.24,
                    'lon' => 42.24,
                ],
            ],
        ], $articleArray);
    }
}
