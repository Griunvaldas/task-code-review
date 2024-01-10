<?php

declare(strict_types=1);

namespace App\Entity;

use App\Model\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=App\EntityRepository\CustomerRepository::class)
 */
class Customer
{

    /**
     *
     * @ORM\Column(name="`id`", type="integer", length=11, nullable=false)
     * @ORM\GeneratedValue()
     * @ORM\Id()
     *
     * @var int
     */
    public int $id;


    /**
     *
     * @ORM\Column(name="`customer_code`", type="string", length=32, nullable=false, unique=true)
     *
     * @var string
     */
    public string $code;


    /**
     *
     * @ORM\Column(name="`notification_type`", type="string", length=32)
     *
     * @var string
     */
    public string $notificationType = Message::TYPE_EMAIL;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getNotificationType(): string
    {
        return $this->notificationType;
    }

    /**
     * @param string $notificationType
     */
    public function setNotificationType(string $notificationType): void
    {
        $this->notificationType = $notificationType;
    }
}