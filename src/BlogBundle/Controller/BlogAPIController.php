<?php

namespace BlogBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Handler\BlogHandler;

class BlogAPIController extends FOSRestController
{
    /**
     * Get Handler Controller.
     **/
    private function getHandler() : BlogHandler
    {
        return $this->get('handler.blog');
    }

    /**
     * Get all posts.
     *
     * @Rest\View()
     * @Rest\QueryParam(name="fields", description="Filter fields with comma", default=null )
     * @Rest\QueryParam(name="page", description="Page", default=1 )
     * @Rest\QueryParam(name="limit", description="Limit rows per page", default=30 )
     * @SWG\Tag(name="blogs")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Blog::class, groups={"full"})
     *     )
     * )
     * @SWG\Parameter(name="fields", in="query",  type="string",description="Filter fields with comma")
     * @SWG\Parameter(name="page",in="query",type="integer",description="Filter page")
     * @SWG\Parameter(name="limit", in="query", type="integer", description="Filter page")
     */
    public function getBlogsAction(Request $request, int $page, int $limit, string $fields)
    {
        $blogQuery = $this->getHandler()->findAllQuery();
        $pager = $this->get('pagination')->paginateQuery($blogQuery, $limit, $page);
        $view = $this->view(iterator_to_array($pager->getCurrentPageResults()), 200);
        $view = $this->get('serializer.custom')->filterFields($view, $fields);
        $view = $this->get('pagination')->paginateHeader($view, $pager, $request->attributes->get('_route'), array('limit' => $limit, 'page' => $page));
        return $this->handleView($view);
    }

    /**
     * Get one posts.
     *
     * @Rest\View()
     * @Rest\QueryParam(name="fields", description="Filter fields with comma", default=null )
     * @SWG\Tag(name="blogs")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     * )
     */
    public function getBlogAction(int $id, string $fields)
    {
        $blog = $this->getHandler()->find($id);

        if (!$blog) {
            $response = array('No content');
            $view = $this->view($response, 204);
            return $this->handleView($view);
        }

        $view = $this->view($blog, 200);
        $view = $this->get('serializer.custom')->filterFields($view, $fields);
        return $this->handleView($view);
    }
}