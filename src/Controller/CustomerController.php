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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    private const PAYLOAD_TYPE = 'json';

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @Route("/customer/{code}/notifications", name="customer_notifications", methods={"GET"})
     */
    public function notifyCustomer(string $code, Request $request): JsonResponse
    {
        try {
            // we try to get customer entity from ORM
            $customer = $this->getCustomerRepository()->findOneBy(['code' => $code]);

            // if customer doesn't exist there is no point for further code execution, so we return 404
            if (!$customer instanceof Customer) {
                return $this->jsonError(['customer' => 'not found'], Response::HTTP_NOT_FOUND);
            }

            // using Symfony serializer component to deserialize provided JSON to DTO
            $requestData = SerializerBuilder::build()->deserialize(
                $request->getContent(),
                NotifyCustomerDto::class,
                self::PAYLOAD_TYPE
            );

            // and use Symfony validator component to check if received data meets our expectations
            $validationErrors = $this->validate($requestData);

            // if there is anything - return them with 422
            if (!empty($validationErrors)) {
                return $this->jsonError($validationErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // we may want to check if customer notificationType matches message type - customer may not want to get
            // SMS if he opted for email, and vice versa :)
            if ($customer->getNotificationType() === $requestData->getType()) {
                // we resolve Sender by customer-provided notification type, and then initialize Messenger
                // with appropiate sender only
                $sender = SenderResolver::resolve($customer->getNotificationType());
                $messenger = new Messenger($sender);

                // we build and send message for customer
                $messenger->send(
                    $this->getMessageBuilder()->build($requestData, $customer)
                );

                // even then, if something wrong happens in code initialization or validation, we check additionally
                // if message has been sent and provide that to our returned payload
                $success = (int)$messenger->isSent();
            } else {
                $success = 0;
            }

            // final output of 200 and results - type and body
            return $this->jsonSuccess($success, [
                'type' => $requestData->getType(),
                'body' => $requestData->getBody(),
            ]);
        } catch (MissingConstructorArgumentsException|\InvalidArgumentException $e) {
            // we can log exception to sentry or any other monitoring/logs service
            // end-user should not see exceptions so we return bad request instead
            return new JsonResponse(['success' => 0], Response::HTTP_BAD_REQUEST);
        }
    }

    protected function validate(NotifyCustomerDto $dto): array
    {
        $errors = $this->validator->validate($dto);

        return count($errors) > 0 ? $this->mapErrors($errors) : [];
    }

    private function mapErrors(ConstraintViolationListInterface $errors): array
    {
        // KISS - done with foreach on purpose for easy readability
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


    // helper methods to prevent lots of duplicated code in parent method
    private function jsonError(array $error, int $code): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => 0,
                'error' => $error
            ],
            $code
        );
    }

    private function jsonSuccess(int $success, array $data = []): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => $success,
                'message' => $data
            ]
        );
    }
}
