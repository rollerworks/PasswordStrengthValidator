<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Tests\P0wnedPassword\Request;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Rollerworks\Component\PasswordStrength\P0wnedPassword\Request\Client;
use Rollerworks\Component\PasswordStrength\P0wnedPassword\Request\Result;

class ClientTest extends TestCase
{
    /** @var HttpClient|MockObject */
    private $client;

    /** @var Client */
    private $checker;

    private $foundResult = '00659D6178E2DAF3D6BE70358A1EB54883A:6
032471D9B185979579C773FDA622293BFFC:24
0371D53107275E34091E29220622B62ED72:2
0593916D0FE39C1CBF870BDA44AA1A6F9A2:15
06ACBE8716FFA0D70E8453AC0CF560B9B22:3
06C866D7AEE7CBB84255BBF5529520D878B:3
08403CDA0395608552144349EB96D30DD5F:28
08E1C1E99DE892ED16CDED45306D8DEDDBF:3
0955820A68920AF7559F89FE2D12EE83357:2
09F74D1DC15112328C8A8ACC90D394AFBE4:7
5F74FD0522862127A00BDEF879C4D9A1A02:4031
0A517529767483361432D4F3663F89BAA7E:2
0C341F894BD4EE961AE874ACD3BC8157825:4
';
    private $notFoundResult = '00659D6178E2DAF3D6BE70358A1EB54883A:6
032471D9B185979579C773FDA622293BFFC:24
0371D53107275E34091E29220622B62ED72:2
0593916D0FE39C1CBF870BDA44AA1A6F9A2:15
06ACBE8716FFA0D70E8453AC0CF560B9B22:3
06C866D7AEE7CBB84255BBF5529520D878B:3
08403CDA0395608552144349EB96D30DD5F:28
08E1C1E99DE892ED16CDED45306D8DEDDBF:3
0955820A68920AF7559F89FE2D12EE83357:2
09F74D1DC15112328C8A8ACC90D394AFBE4:7
0A517529767483361432D4F3663F89BAA7E:2
0C341F894BD4EE961AE874ACD3BC8157825:4
';

    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClient::class);
        $this->checker = new Client($this->client, new NullLogger());
    }

    public function testResponseWithFoundResult()
    {
        $password = 'correctbatteryhorse';
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())->method('getStatusCode')->willReturn(200);
        $responseMock->expects($this->once())->method('getBody')->willReturn($this->foundResult);
        $request = new Request('GET', 'https://api.pwnedpasswords.com/range/C4FA0');
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($responseMock);

        $result = $this->checker->check($password);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->wasFound());
        $this->assertEquals(4031, $result->getUseCount());
    }

    public function testResponseWithoutFoundResult()
    {
        $password = 'correctbatteryhorse';
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())->method('getStatusCode')->willReturn(200);
        $responseMock->expects($this->once())->method('getBody')->willReturn($this->notFoundResult);
        $request = new Request('GET', 'https://api.pwnedpasswords.com/range/C4FA0');
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($responseMock);

        $result = $this->checker->check($password);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->wasFound());
        $this->assertEquals(0, $result->getUseCount());
    }

    public function testNon200Response()
    {
        $password = 'correctbatteryhorse';
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())->method('getStatusCode')->willReturn(404);
        $responseMock->expects($this->never())->method('getBody');
        $request = new Request('GET', 'https://api.pwnedpasswords.com/range/C4FA0');
        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($responseMock);

        $result = $this->checker->check($password);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->wasFound());
        $this->assertEquals(0, $result->getUseCount());
    }
}
