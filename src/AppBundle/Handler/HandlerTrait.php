<?php

namespace AppBundle\Handler;

trait HandlerTrait
{

    public function findAll(){
        $this->repository->findAll();
    }

    public function findAllBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    public function find($id)
    {
        return $this->repository->findOneBy(array('id' => $id));
    }

    public function findBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

}