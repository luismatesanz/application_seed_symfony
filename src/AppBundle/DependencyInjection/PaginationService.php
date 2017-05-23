<?php

namespace AppBundle\DependencyInjection;

use FOS\RestBundle\View\View;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Routing\Router;

class PaginationService
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Paginate Query ORM
     */
    public function paginateQuery($query, int $limit, int $page, ?bool $useOutputWalkers = null, bool $fetchJoinCollection = true ) : Pagerfanta
    {
        // useOutputWalkers: para consultas con campos personalizados en la query, sino solo funciona con objetos
        $pagerAdapter = new DoctrineORMAdapter($query, $fetchJoinCollection, $useOutputWalkers);
        $pager = new Pagerfanta($pagerAdapter);

        if ($pager->getNbResults() > 0) :
            if ($limit == 0) :
                $pager->setMaxPerPage($pager->getNbResults());
            else :
                $pager->setMaxPerPage($limit);
                $pager->setCurrentPage($page);
            endif;
        endif;

        return $pager;
    }

    /**
     * Paginate Array
     *
     */
    public function paginateArray(array $array, int $limit, int $page ) : Pagerfanta
    {
        $pagerAdapter = new ArrayAdapter($array);
        $pager = new Pagerfanta($pagerAdapter);

        if ($pager->getNbResults() > 0) :
            if ($limit == 0) :
                $pager->setMaxPerPage($pager->getNbResults());
            else :
                $pager->setMaxPerPage($limit);
                $pager->setCurrentPage($page);
            endif;
        endif;

        return $pager;
    }

    /**
     * Set headers response
     */
    public function paginateHeader(View $view, Pagerfanta $pager, string $nameRoute, array $paramsRoute) : View
    {
        $view->setHeader('X-Page', $pager->getCurrentPage());
        $view->setHeader('X-Limit', $pager->getMaxPerPage());
        $view->setHeader('X-Pages-Count', $pager->getNbPages());
        $view->setHeader('X-Total-Count', $pager->getNbResults());

        $paramsRoute['limit'] = $pager->getMaxPerPage();

        $paramsRouteFirst = $paramsRoute;
        $paramsRouteLast = $paramsRoute;
        $paramsRouteNext = $paramsRoute;
        $paramsRoutePrevious = $paramsRoute;

        // FIRST
        $paramsRouteFirst['page'] = 1;

        // LAST
        $paramsRouteLast['page'] = $pager->getNbPages();

        // NEXT
        $paramsRouteNext['page'] = ($pager->hasNextPage()) ? $pager->getNextPage() : $pager->getCurrentPage();

        // PREVIOUS
        $paramsRoutePrevious['page'] = ($pager->hasPreviousPage()) ? $pager->getPreviousPage() : $pager->getCurrentPage();

        // first, last, next, previous
        $view->setHeader('X-Link-First',  $this->router->generate($nameRoute, $paramsRouteFirst, true));
        $view->setHeader('X-Link-Last',  $this->router->generate($nameRoute, $paramsRouteLast, true));
        $view->setHeader('X-Link-Next',  $this->router->generate($nameRoute, $paramsRouteNext, true));
        $view->setHeader('X-Link-Previous',  $this->router->generate($nameRoute, $paramsRoutePrevious, true));

        return $view;
    }
}
