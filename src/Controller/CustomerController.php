<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Customer\NotifyCustomerDto;
use App\Entity\Customer;
use App\Service\Messenger;
use App\Service\Sender\SenderResolver;
use App\System\Customer\CustomerMessageBuilder;
use App\System\SerializerBuilder;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *
 */
class CustomerController extends AbstractController
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     *
     * @Route("/customer/{code}/notifications", name="customer_notifications", methods={"GET"})
     */
    public function notifyCustomer(string $code, Request $request): JsonResponse
    {
        try {
            $requestData = SerializerBuilder::build()->deserialize(
                $request->getContent(),
                NotifyCustomerDto::class,
                'json'
            );

            $validationResult = $this->validate($requestData);

            if (!empty($validationResult)) {
                return new JsonResponse(['errors' => $validationResult], Response::HTTP_BAD_REQUEST);
            }

            $customer = $this->getCustomerRepository()->findOneBy(['code' => $code]);

            if (!$customer instanceof Customer) {
                throw new NotFoundHttpException(
                    'Customer not found',
                    null,
                    Response::HTTP_NOT_FOUND
                );
            }

            if ($customer->getNotificationType() === $requestData->getType()) {
                $message = $this->getMessageBuilder()->build($requestData, $customer);
                $messenger = new Messenger(SenderResolver::resolve($customer->getNotificationType()));
                $messenger->send($message);
            }

            return new JsonResponse('OK');
        } catch (NotFoundHttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        } catch (MissingConstructorArgumentsException $e) {
            return new JsonResponse('Bad request', Response::HTTP_BAD_REQUEST);
        }
    }

    protected function validate(NotifyCustomerDto $dto): array
    {
        $errors = $this->validator->validate($dto);

        return count($errors) > 0 ? $this->mapErrors($errors) : [];
    }

    private function mapErrors(ConstraintViolationListInterface $errors): array
    {
        $violations = [];

        foreach ($errors as $error) {
            $violations[$error->getPropertyPath()] = $error->getMessage();
        }

        return $violations;
    }

    private function getCustomerRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository(Customer::class);
    }

    private function getMessageBuilder(): CustomerMessageBuilder
    {
        return new CustomerMessageBuilder();
    }
}
