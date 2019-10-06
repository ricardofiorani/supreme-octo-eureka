<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\SlackRequestHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class PingHandlerTest extends TestCase
{
    public function testResponse()
    {
        $pingHandler = new SlackRequestHandler();
        $response = $pingHandler->handle(
            $this->prophesize(ServerRequestInterface::class)->reveal()
        );

        $json = json_decode((string) $response->getBody());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertTrue(isset($json->ack));
    }
}
