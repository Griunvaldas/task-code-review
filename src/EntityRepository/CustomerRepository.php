<?php

namespace App\EntityRepository;

use App\Entity\Customer;
use Doctrine\ORM\EntityRepository;

class CustomerRepository extends EntityRepository
{
    /**
     * {@inheritDoc}
     */
    public function getEntityClass(): string
    {
        return Customer::class;
    }
}