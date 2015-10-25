<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Property;
use Doctrine\ORM\EntityRepository;

class PropertyRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('name' => 'ASC'));
    }   

    public function findAllJoins()
    {
        $query = $this->getEntityManager()
                      ->createQueryBuilder()
                      ->select('p, i, s')
                      ->from('AppBundle:Property', 'p')
                      ->leftJoin('p.images', 'i')
                      ->leftJoin('p.site', 's')
                      ->orderBy('s.name', 'ASC')
                      ->addOrderBy('p.name', 'ASC')
                      ->addOrderBy('i.imageName', 'ASC')
                      ->getQuery();
        return $query->getResult();
    }

    public function getByIdJoins($id){
    	$query = $this->getEntityManager()
                      ->createQueryBuilder()
                      ->select('p, i, s')
                      ->from('AppBundle:Property', 'p')
                      ->leftJoin('p.images', 'i')
                      ->leftJoin('p.site', 's')
                      ->where('p.id = ?1')
                      ->orderBy('i.imageName', 'ASC')
                      ->setParameter(1, $id)
                      ->getQuery();
        return $query->getSingleResult();
    } 
}