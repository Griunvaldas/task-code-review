<?php

namespace App\Entity;

use App\Model\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Customer
{

    /**
     *
     * @ORM\Column(name="`customer_code`", type="string", length=32, nullable=false)
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
    public string $notification_type = Message::TYPE_EMAIL;

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
        return $this->notification_type;
    }

    /**
     * @param string $notification_type
     */
    public function setNotificationType(string $notification_type): void
    {
        $this->notification_type = $notification_type;
    }
}