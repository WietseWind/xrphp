<?php

namespace XRPhp;

use GuzzleHttp\Client;

/**
 *  A rippled http/https connection class
 *
 *  This class is used to send JSON-RPC commands to a rippled server.
 */
class Connection
{
    /** @var Client */
    private $client;

    /** @var string */
    private $endpoint;

    /** @var string Hostname or IP of the endpoint */
    private $host;

    /** @var int Port of the endpoint */
    private $port;

    /** @var @var string http or https */
    private $scheme;

    /**
     * Connection constructor.
     *
     * @param mixed $config
     * @throws \OutOfBoundsException
     */
    public function __construct($config)
    {
        $type = gettype($config);

        switch ($type) {
            case 'string':
                $this->endpoint = $config;
                break;

            case 'array':
                $this->endpoint = $config['endpoint'] ?? null;
                break;

            default:
                throw new \InvalidArgumentException('Constructor argument must be endpoint string or config array');

        }

        if ($this->endpoint !== null) {
            $parts = parse_url($this->endpoint);
            if ($parts === false) {
                throw new \InvalidArgumentException('Invalid endpoint format');
            }

            $this->scheme = $parts['scheme'];
            $this->host = $parts['host'];
            $this->port = $parts['port'] ?? null;

        } else {
            $this->scheme = $config['scheme'] ?? null;
            $this->host = $config['host'] ?? null;
            $this->port = $config['port'] ?? null;
        }

        if (empty($this->scheme)) {
            throw new \InvalidArgumentException('scheme is required if endpoint is not supplied');
        }

        // Validate protocol
        if (!\in_array($this->scheme, ['http', 'https'])) {
            throw new \OutOfBoundsException('scheme must be http or https');
        }

        if (empty($this->host)) {
            throw new \InvalidArgumentException('host is required if endpoint is not supplied');
        }

        // Auto set port based on protocol if it has not been passed in
        $port = $config['port'] ?? ($this->scheme === 'https' ? 443 : 80);

        // Validate port
        if ($port < 1 || $port >= 65535) {
            throw new \OutOfBoundsException('port must be between 1 and 65535');
        }

        $this->port = $port;

        // Setup Guzzle client
        $this->client = new Client(['headers' => ['Content-Type' => 'application/json']]);

        // Build endpoint
        if (empty($this->endpoint)) {
            $this->endpoint = $this->scheme . '://' . $this->host . ':' . $this->port;
        }
    }

    public function send(string $method, array $params = null)
    {
        $json = $this->prepareRequest($method, $params);
        $response = $this->client->post($this->endpoint,
            ['body' => $json]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function prepareRequest(string $method, array $params = null): string
    {
        if ($params === null) {
            $params = new \stdClass();
        }
        $request = ['method' => $method, 'params' => []];
        $request['params'][] = $params;
        return json_encode($request);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): void
    {
        $this->scheme = $scheme;
    }
}
