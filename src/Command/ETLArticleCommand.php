<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Model\ETL\Article\ArticleETLBuilder;
use Doctrine\ORM\EntityManagerInterface;
use ElasticsearchETL\AbstractETLCommand;
use Monolog\Handler\NullHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Monolog\Logger;

class ETLArticleCommand extends AbstractETLCommand
{
    /**
     * ETLArticleCommand constructor.
     */
    public function __construct(ArticleETLBuilder $ETLArticleBuilder, EntityManagerInterface $em, LoggerInterface $logger)
    {
        //desactivate logs
        if ($logger instanceof Logger) {
            $logger->pushHandler(new NullHandler());
        }
        $em->getConfiguration()->setSQLLogger(null);

        parent::__construct('app:etl:article');

        $this->ETLBuilder = $ETLArticleBuilder;
    }
}
