<?php

namespace PrepayMatch\Components\Banking\Proxy;

use DateTime;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
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

    public function __construct(ClientInterface $client, $token)
    {
        $this->client = $client;
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
        $options = $this->getOptions($query);

        try {
            $response = $this->client->get('transaction', $options);
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
     * @param DateTime      $from
     * @param DateTime|null $to
     *
     * @return array
     */
    private function buildQuery(DateTime $from, DateTime $to = null)
    {
        $params = ['from' => $from->format('d.m.Y')];
        if($to !== null) {
            $params += ['to' => $to->format('d.m.Y')];
        }

        return $params;
    }

    /**
     * @param array $query
     *
     * @return array
     */
    private function getOptions(array $query)
    {
        return [
            'query' => $query,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ];
    }
}