<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\SearchArticleType;
use App\Model\ElasticSearchClient;
use App\Model\Search\DTO\ArticleSearch;
use App\Model\Search\Transformer\SearchToArticleQueryTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @var ElasticSearchClient
     */
    protected $client;

    /**
     * @var SearchToArticleQueryTransformer
     */
    protected $transformer;

    /**
     * BlogController constructor.
     */
    public function __construct(ElasticSearchClient $client, SearchToArticleQueryTransformer $transformer)
    {
        $this->client = $client;
        $this->transformer = $transformer;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(Request $request)
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/search", name="front_article_search")
     */
    public function search(Request $request)
    {
        $form = $this->createForm(SearchArticleType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData();
        } else {
            $search = new ArticleSearch();
        }

        $query = $this->transformer->transform($search);
        $result = $this->client->search($query);

        return $this->render('blog/search.html.twig', [
            'form' => $form->createView(),
            'articles' => $result['hits']['hits'],
        ]);
    }

    /**
     * @Route("/article/add", name="front_article_add")
     *
     * just an example, there is EasyAdminBundle
     */
    public function articleCreate(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('succes', 'your message');

            return $this->redirectToRoute('article_list'); //create the action article_list
        }

        return $this->render('blog/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
