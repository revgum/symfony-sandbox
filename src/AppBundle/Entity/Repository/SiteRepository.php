<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Site;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class SiteRepository extends EntityRepository
{
  public function findAllJoinsById($id)
  {
    $query = $this->joinQuery()
                  ->where('s.id = ?1')
                  ->orderBy('s.name', 'ASC')
                  ->addOrderBy('p.name', 'ASC')
                  ->addOrderBy('i.imageName', 'ASC')
                  ->setParameter(1, $id)
                  ->getQuery();
    return $query->getSingleResult();
  }

  public function findAllStartsWith($letter, $page, $per_page)
  {
    $query = $this->joinQuery();

    if($letter != 'ALL'){
        $query->where('s.name LIKE :letter')
              ->setParameter('letter', "{$letter}%");
    }

    $query->orderBy('s.name', 'ASC')
          ->addOrderBy('p.name', 'ASC')
          ->addOrderBy('i.imageName', 'ASC');

    $paginator = new Paginator($query, $fetchJoinCollection = true);
    $paginator->getQuery()
              ->setFirstResult(($page - 1) * $per_page)
              ->setMaxResults($per_page);
    return $paginator;
  }

  public function findAll()
  {
    return $this->findBy(array(), array('name' => 'ASC'));
  }    

  private function joinQuery()
  {
    return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('s, p, i')
                ->from('AppBundle:Site', 's')
                ->leftJoin('s.properties', 'p')
                ->leftJoin('p.images', 'i');
  }
}