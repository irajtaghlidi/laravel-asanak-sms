<?php

namespace NotificationChannels\AsanakSms;

use Illuminate\Support\Arr;
use GuzzleHttp\Client as HttpClient;
use NotificationChannels\AsanakSms\Exceptions\CouldNotSendNotification;

class AsanakSmsApi
{

    /** @var HttpClient */
    protected $client;

    /** @var string */
    protected $endpoint;

    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /** @var string */
    protected $from;

    public function __construct(array $config)
    {
        // dd($config);

        $this->username = Arr::get($config, 'username');
        $this->password = Arr::get($config, 'password');
        $this->from = Arr::get($config, 'from');
        $this->endpoint = Arr::get($config, 'apiurl', 'http://panel.asanak.com/webservice/v1rest/sendsms');

        $this->client = new HttpClient([
            'timeout' => 10,
            'connect_timeout' => 10,
            'verify' => false,
        ]);
    }

    public function send($params)
    {
        $base = [
            // 'charset' => 'utf-8',
            'username'   => $this->username,
            'password'   => $this->password,
            'source'     => $this->from,
        ];

        $params = \array_merge($base, \array_filter($params));

        try {
            $response = $this->client->request('POST', $this->endpoint, ['form_params' => $params, 'track_redirects' => true]);

            $response = \json_decode((string) $response->getBody(), true);

            if (isset($response['error'])) {
                throw new \DomainException($response['error'], $response['error_code']);
            }

            return $response;
        } catch (\DomainException $exception) {
            throw CouldNotSendNotification::smscRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithSmsc($exception);
        }
    }
}
