<?php
namespace Customize\Repository;

use Eccube\Repository\CustomerRepository as BaseRepository;

class CustomerRepository extends BaseRepository
{
    /**
     * 会員を返す.
     *
     * @return Customer[]
     */
    public function loadUser()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c.firebase_uid');

        return $qb->getQuery()->getResult();
    }
}