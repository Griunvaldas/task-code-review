<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Customer\NotifyCustomerDto;
use App\Entity\Customer;
use App\Service\Customer\EntityGetter;
use App\Service\Exceptions\CustomerNotFoundException;
use App\Service\Exceptions\ValidationException;
use App\Service\Messenger;
use App\Service\Sender\SenderResolver;
use App\System\Customer\CustomerMessageBuilder;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends RestController
{
    private const PAYLOAD_TYPE = 'json';

    /**
     *  @Route("/api/v1/customer/{code}/notifications", name="customer_notifications", methods={"POST"})
     *  @OA\Post(
     *      path="/api/v1/customer/{code}/notifications",
     *      summary="Notify a customer",
     *      operationId="notifyCustomer",
     *      tags={"Customer"},
     *      @OA\Parameter(name="code", in="path", description="Customer code", required=true, @OA\Schema(type="string")),
     *      @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=NotifyCustomerDto::class))),
     *      @OA\Response(
     *          response=200,
     *          description="Notification sent successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="integer"),
     *              @OA\Property(property="message", type="object",
     *              @OA\Property(property="type", type="string"),
     *              @OA\Property(property="body", type="string")
     *            )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="integer")
     *          )
     *      ),
     *      @OA\Response(
     *       response=404,
     *       description="Customer not found",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="success", type="integer", example=0),
     *           @OA\Property(property="error", type="object",
     *               @OA\Property(property="customer", type="string", example="not found")
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              type="object",
     *                @OA\Property(property="success", type="integer", example=0),
     *                @OA\Property(property="error", type="object",
     *                @OA\Property(property="body", type="string", example="body cannot be empty")
     *            )
     *          )
     *      ),
     *      security={{"apiKey": {}}}
     *  )
     * /
     */
    public function notifyCustomer(string $code, Request $request): JsonResponse
    {
        try {
            // we try to get customer entity from ORM
            $customer = $this->getCustomer($code);

            // using Symfony serializer component to deserialize provided JSON to DTO
            $requestData = $this->getRequestData($request);

            // and use Symfony validator component to check if received data meets our expectations
            $this->validate($requestData, $this->validator);

            // we may want to check if customer notificationType matches message type - customer may not want to get
            // SMS if he opted for email, and vice versa :)
            if ($customer->getNotificationType() === $requestData->getType()) {
                // we resolve Sender by customer-provided notification type, and then initialize Messenger
                // with appropiate sender only
                $sender = SenderResolver::resolve($customer->getNotificationType());
                $messenger = new Messenger($sender);


                // we build and send message for customer
                $messenger->send(
                    $this->getMessageBuilder()->build($requestData)
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
        } catch (CustomerNotFoundException $e) {
            return $this->jsonError(['customer' => 'not found'], $e->getCode());
        } catch (ValidationException $e) {
            return $this->jsonError($this->validationErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (MissingConstructorArgumentsException|\InvalidArgumentException $e) {
            return new JsonResponse(['success' => 0], Response::HTTP_BAD_REQUEST);
        }
    }

    protected function validate(NotifyCustomerDto $dto, ValidatorInterface $validator): void
    {
        $validation = $this->mapValidationErrors(
            $validator->validate($dto)
        );

        if (!empty($validation)) {
            throw new ValidationException(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function getRequestData(Request $request): NotifyCustomerDto
    {
        return $this->serializer->deserialize(
            $request->getContent(),
            NotifyCustomerDto::class,
            self::PAYLOAD_TYPE
        );
    }

    private function getCustomer(string $code): Customer
    {
        return (new EntityGetter($this->getDoctrine()))->get($code);
    }

    private function getMessageBuilder(): CustomerMessageBuilder
    {
        return new CustomerMessageBuilder();
    }
}
