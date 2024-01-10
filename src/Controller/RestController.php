<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class RestController extends AbstractController
{
    protected ValidatorInterface $validator;
    protected SerializerInterface $serializer;
    protected array $validationErrors = [];

    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    protected function jsonError(array $error, int $code): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => 0,
                'error' => $error
            ],
            $code
        );
    }

    protected function jsonSuccess(int $success, array $data = []): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => $success,
                'message' => $data
            ]
        );
    }

    protected function mapValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $violations = [];

        foreach ($errors as $error) {
            $violations[$error->getPropertyPath()] = $error->getMessage();
        }

        return $this->validationErrors = $violations;
    }
}
