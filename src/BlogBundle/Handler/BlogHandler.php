<?php

namespace BlogBundle\Handler;

use AppBundle\Handler\HandlerTrait;
use BlogBundle\Entity\Blog;
use BlogBundle\Repository\BlogRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Constraints\DateTime;

class BlogHandler
{
    private $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    use HandlerTrait;

    public function findAllQuery() : QueryBuilder
    {
        return $this->repository->findAllQuery();
    }

    public function find(int $id) : Blog
    {
        $blog = $this->repository->find($id);
        $blog = $this->calculateStatus($blog);
        return $blog;
    }

    private function calculateStatus(Blog $blog) : Blog
    {
        $blog->setStatus("open");
        $today = new \DateTime();
        if ($blog->getDate() < $today){
            $blog->setStatus("lapsed");
        }
        return $blog;
    }



}