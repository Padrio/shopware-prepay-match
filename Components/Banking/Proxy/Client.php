<?php

namespace PrepayMatch\Components\Banking\Proxy;

use DateTime;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Client
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $host;

    public function __construct(ClientInterface $client, $host, $token)
    {
        $this->client = $client;
        $this->host = $host;
        $this->token = $token;
    }

    /**
     * @param DateTime      $from
     * @param DateTime|null $to
     *
     * @return array
     */
    public function getTransactions(DateTime $from, DateTime $to = null)
    {
        $query = $this->buildQuery($from, $to);
        $uri = $this->getUri($query);

        try {
            $response = $this->client->get($uri, $this->getOptions());
        } catch (RequestException $e) {
            // Todo: Logging.
            return null;
        }

        try {
            return $this->decodeResponse($response);
        } catch (Exception $e) {
            // Todo: Logging.
            return null;
        }
    }

    /**
     * @param ResponseInterface $response
     * @param array             $expectedStatusCodes
     *
     * @throws Exception
     */
    private function checkExpectedResponseCode(ResponseInterface $response, array $expectedStatusCodes = [200])
    {
        if (!in_array($response->getStatusCode(), $expectedStatusCodes)) {
            throw new Exception('Unexpected status code occured: '. $response->getStatusCode());
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array
     * @throws Exception
     */
    private function decodeResponse(ResponseInterface $response)
    {
        $this->checkExpectedResponseCode($response);
        $body = $response->getBody()->getContents();

        $decoded = \json_decode($body, true);
        if($decoded === false) {
            throw new Exception('Failed to decode response body: '. json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * @return string
     */
    private function getUri($query = '')
    {
        return sprintf('%s/transaction%s', $this->host, $query);
    }

    /**
     * @param DateTime      $from
     * @param DateTime|null $to
     *
     * @return string
     */
    private function buildQuery(DateTime $from, DateTime $to = null)
    {
        $params = ['from' => $from];
        if($to !== null) {
            $params += ['to' => $to];
        }

        return '?' . http_build_query($params);
    }

    /**
     * @return array
     */
    private function getOptions()
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer' . $this->token,
            ],
        ];
    }
}