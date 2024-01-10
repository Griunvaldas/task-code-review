<?php

namespace App\Tests;

use App\Model\Message;
use App\Service\Sender\SenderResolver;
use PHPUnit\Framework\TestCase;

class SenderResolverTest extends TestCase
{
    /**
     * @dataProvider resolveDataProvider
     * @param bool $expected
     * @param string $type
     * @param Message $message
     * @return void
     */
    public function testResolve(bool $expected, string $type, Message $message): void
    {
        $this->assertEquals(
            $expected,
            SenderResolver::resolve($type)->supports($message)
        );
    }

    /**
     * @dataProvider invalidTypeDataProvider
     * @param string $invalidType
     * @return void
     */
    public function testInvalidType(string $invalidType): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SenderResolver::resolve($invalidType);
    }

    public function invalidTypeDataProvider(): \Generator
    {
        yield '#1 - invalid type' => ['invalid_type'];

        yield '#2 - empty type' => [''];
    }


    public function resolveDataProvider(): \Generator
    {
        yield '#1 - email type, message sent as email' => [
            'expected' => true,
            'type' => 'email',
            'message' => $this->mockMessage('email')
        ];

        yield '#2 - email type, message sent as sms' => [
            'expected' => false,
            'type' => 'email',
            'message' => $this->mockMessage('sms')
        ];

        yield '#3 - sms type, message sent as email' => [
            'expected' => false,
            'type' => 'sms',
            'message' => $this->mockMessage('email')
        ];

        yield '#4 - sms type, message sent as sms' => [
            'expected' => true,
            'type' => 'sms',
            'message' => $this->mockMessage('sms')
        ];
    }

    private function mockMessage(string $type): Message
    {
        return (new Message())->setType($type)->setBody('body');
    }
}
