<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Entity\Article;
use App\Model\ElasticSearchClient;
use App\Model\ETL\Article\ArticleETLBuilder;
use App\Model\ETL\Article\ArticleLoad;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class SearchIndexerSubscriber implements EventSubscriber
{
    /**
     * @var ArticleETLBuilder
     */
    protected $articleETLBuilder;

    /**
     * @var ElasticSearchClient
     */
    protected $clientElasticSearch;

    public function __construct(ArticleETLBuilder $articleETLBuilder, ElasticSearchClient $clientElasticSearch)
    {
        $this->articleETLBuilder = $articleETLBuilder;
        $this->clientElasticSearch = $clientElasticSearch;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Article && true === $entity->isIndexable) {
            $this->clientElasticSearch->delete([
                'index' => ArticleLoad::getAlias(),
                'type' => ArticleLoad::getAlias(),
                'id' => $entity->getId(),
            ]);
        }
    }

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Article && true === $entity->isIndexable) {
            $this->articleETLBuilder->build()->indexOne($entity);
        }
    }
}
