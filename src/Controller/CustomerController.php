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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;

/**
 *
 */
class CustomerController extends AbstractController
{

    /**
     *
     * @Route("/customer/{code}/notifications", name="customer_notifications", methods={"GET"})
     */
    public function notifyCustomer(string $code, Request $request): Response
    {
        try {
            $requestData = SerializerBuilder::build()->deserialize(
                $request->getContent(),
                NotifyCustomerDto::class,
                'json'
            );

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

            return new Response('OK');
        } catch (NotFoundHttpException $e) {
            return new Response($e->getMessage(), $e->getCode());
        } catch (MissingConstructorArgumentsException $e) {
            return new Response('Bad request', Response::HTTP_BAD_REQUEST);
        }
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
