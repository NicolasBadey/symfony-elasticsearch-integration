<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\tests\Model\ETL\Article;

use App\Entity\Article;
use App\Model\ElasticSearchClient;
use App\Model\ETL\Article\ArticleLoad;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Output\NullOutput;

class ArticleETLTest extends KernelTestCase
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

    public function testBuildAndRun()
    {
        self::bootKernel();
        $container = self::$container;

        $etlBuilder = $container->get('App\Model\ETL\Article\ArticleETLBuilder');
        /**
         * @var ElasticSearchClient
         */
        $esClient = $container->get('App\Model\ElasticSearchClient');

        $output = new NullOutput();

        /*
         * @var $etlBuilder \App\Model\ETL\Article\ArticleETLBuilder
         */
        $etlBuilder->build()->run($output);

        //test is too fast, we have to refresh
        $esClient->refresh(['index' => ArticleLoad::getAlias()]);

        $nbResult = $esClient->count([
            'index' => ArticleLoad::getAlias(),
        ]);

        $this->assertSame(10, $nbResult['count']);
    }

    public function testRunOne()
    {
        self::bootKernel();
        $container = self::$container;

        $etlBuilder = $container->get('App\Model\ETL\Article\ArticleETLBuilder');

        $output = new NullOutput();

        $articles = $container->get('App\Repository\ArticleRepository')->findAll();

        /*
         * @var $etlBuilder \App\Model\ETL\Article\ArticleETLBuilder
         */
        sleep(1); //avoid same timestamp
        $etlBuilder->build()->run($output, false, [$articles[0]->getId()]);

        /**
         * @var ElasticSearchClient
         */
        $esClient = $container->get('App\Model\ElasticSearchClient');

        //test is too fast, we have to refresh
        $esClient->refresh(['index' => ArticleLoad::getAlias()]);

        $nbResult = $esClient->count([
            'index' => ArticleLoad::getAlias(),
        ]);

        $this->assertSame(1, $nbResult['count']);
    }

    public function testAddOneInLiveMode()
    {
        self::bootKernel();
        $container = self::$container;

        $esClient = $container->get('App\Model\ElasticSearchClient');

        $etlBuilder = $container->get('App\Model\ETL\Article\ArticleETLBuilder');

        $output = new NullOutput();

        $article = new Article();
        $article->setTitle('42');
        $article->setContent('42');
        $article->setLatitude('42');
        $article->setLongitude('42');
        $article->isIndexable = false; //disable subscriber

        $container->get('doctrine')->getManager()->persist($article);
        $container->get('doctrine')->getManager()->flush();

        //test is too fast, we have to refresh
        $esClient->refresh(['index' => ArticleLoad::getAlias()]);

        $nbResult = $esClient->count([
            'index' => ArticleLoad::getAlias(),
        ]);

        //there is still the article of the previous test
        $this->assertSame(1, $nbResult['count']);

        /*
         * @var $etlBuilder \App\Model\ETL\Article\ArticleETLBuilder
         *
         * we add another article on live index
         */
        $etlBuilder->build()->run($output, true, [$article->getId()]);

        //test is too fast, we have to refresh
        $esClient->refresh(['index' => ArticleLoad::getAlias()]);

        $nbResult = $esClient->count([
            'index' => ArticleLoad::getAlias(),
        ]);

        //there is two article now
        $this->assertSame(2, $nbResult['count']);

        sleep(1); //avoid same timestamp
        //we erase the index and populate one
        $etlBuilder->build()->run($output, false, [$article->getId()]);

        //test is too fast, we have to refresh
        $esClient->refresh(['index' => ArticleLoad::getAlias()]);

        $nbResult = $esClient->count([
            'index' => ArticleLoad::getAlias(),
        ]);

        //there is one article now
        $this->assertSame(1, $nbResult['count']);
    }

    public function testIndexOne()
    {
        self::bootKernel();
        $container = self::$container;

        $article1 = $this->getArticleMock(42);
        $article2 = $this->getArticleMock(43);
        $article3 = $this->getArticleMock(44);

        /**
         * @var \App\Model\ETL\Article\ArticleETLBuilder
         */
        $etlBuilder = $container->get('App\Model\ETL\Article\ArticleETLBuilder');
        $etl = $etlBuilder->build();

        /**
         * @var ElasticSearchClient
         */
        $esClient = $container->get('App\Model\ElasticSearchClient');

        $realIndexName = $esClient->getIndexNameFromAlias(ArticleLoad::getAlias());

        $esClient->indices()->delete(['index' => $realIndexName]);

        $etl->indexOne($article1->reveal(), false);

        $this->expectException(Missing404Exception::class);
        $esClient->count([
            'index' => ArticleLoad::getAlias(),
        ]);

        $etl->indexOne($article2->reveal());

        $esClient->refresh(['index' => ArticleLoad::getAlias()]);

        $nbResult = $esClient->count([
            'index' => ArticleLoad::getAlias(),
        ]);

        $this->assertSame(1, $nbResult['count']);

        $etl->indexOne($article3->reveal());

        $esClient->refresh(['index' => ArticleLoad::getAlias()]);

        $nbResult = $esClient->count([
            'index' => ArticleLoad::getAlias(),
        ]);

        $this->assertSame(2, $nbResult['count']);
    }

    public function getArticleMock($id)
    {
        $article = $this->prophet->prophesize('App\Entity\Article');

        $article->getId()->willReturn($id);
        $article->getLongitude()->willReturn(42.24);
        $article->getLatitude()->willReturn(42.24);
        $article->getCategory()->willReturn(null);
        $article->getContent()->willReturn('lorem');
        $article->getTitle()->willReturn('title42');

        return $article;
    }
}
