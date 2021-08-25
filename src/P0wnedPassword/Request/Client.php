<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\P0wnedPassword\Request;

use GuzzleHttp\Psr7\Request;
use Http\Client\Exception as HttpException2;
use Http\Client\Exception\HttpException;
use Http\Client\HttpClient;
use Psr\Log\LoggerInterface;

/**
 * @internal
 *
 * @final
 */
class Client
{
    /** @var HttpClient */
    private $client;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(HttpClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param $password
     *
     * @throws \Http\Client\Exception
     *
     * @return Result
     */
    public function check($password)
    {
        $hashedPassword = mb_strtoupper(sha1($password));
        $checkHash = mb_substr($hashedPassword, 0, 5);

        try {
            $response = $this->client->sendRequest(new Request('GET', 'https://api.pwnedpasswords.com/range/' . $checkHash));

            if ($response->getStatusCode() === 200) {
                $rowResults = explode("\n", (string) $response->getBody());
                $searchHash = mb_substr($hashedPassword, 5);

                foreach ($rowResults as $result) {
                    if (mb_strpos($result, $searchHash) !== false) {
                        $res = explode(':', $result);

                        return new Result(trim($res[1]));
                    }
                }
            }
        } catch (HttpException $exception) {
            $this->logger->error('HTTP Exception: ' . $exception->getMessage());
        } catch (HttpException2 $exception) {
            $this->logger->error('HTTP Exception: ' . $exception->getMessage());
        } catch (\Exception $exception) {
            $this->logger->error('Exception: ' . $exception->getMessage());
        }

        return new Result(0);
    }
}
