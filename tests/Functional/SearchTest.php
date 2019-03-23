<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Functional;

use App\Model\ElasticSearchClient;
use App\Model\ETL\Article\ArticleLoad;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Panther\PantherTestCase;

class SearchTest extends PantherTestCase
{
    public function testSearch()
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

        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/search');

        $form = $crawler->filter('form[name=search_article]')->form([
            'search_article[search]' => 'lorem',
        ]);
        $crawler = $client->submit($form);

        $this->assertStringContainsString('Lorem ipsum dolor', $crawler->html());
    }
}
