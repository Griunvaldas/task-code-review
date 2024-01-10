<?php

declare(strict_types=1);

namespace App\Service\Customer;

use App\Entity\Customer;
use App\Service\Exceptions\CustomerNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class EntityGetter
{
    private ManagerRegistry $entityManager;

    public function __construct(
        ManagerRegistry $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function get(string $code): Customer
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['code' => $code]);

        if (!$customer instanceof Customer) {
            throw new CustomerNotFoundException(Response::HTTP_NOT_FOUND);
        }

        return $customer;
    }
}
