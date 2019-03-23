<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\tests\Model\ETL\Article;

use Pagerfanta\Adapter\AdapterInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticleExtractTest extends KernelTestCase
{
    public function testGetAdapter()
    {
        self::bootKernel();
        $container = self::$container;

        $adapter = $container->get('App\Model\ETL\Article\ArticleExtract')->getAdapter();

        $this->assertInstanceOf(AdapterInterface::class, $adapter);
    }
}
