<?php

namespace App\EntityRepository;

use App\Entity\Customer;
use Doctrine\ORM\EntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @ORM\Entity
 */
class CustomerRepository extends ServiceEntityRepository
{
    /**
     * {@inheritDoc}
     */
    public function getEntityClass(): string
    {
        return Customer::class;
    }
}