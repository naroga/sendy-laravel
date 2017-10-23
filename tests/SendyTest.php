<?php

namespace Naroga\Tests\Sendy;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

use Naroga\Sendy\Sendy;

/**
 * Class SendyTest
 *
 * @package Hocza\Tests\Sendy
 */
class SendyTest extends TestCase
{
    private $config;

    public function setUp()
    {
        (new Dotenv(__DIR__))->load();

        $this->config = [
            'listId' => env('SENDY_LIST_ID'),
            'installationUrl' => env('SENDY_URL'),
            'apiKey' => env('SENDY_API_KEY'),
        ];
    }

    public function testSimpleSubscribe()
    {
        $subscriber = new Sendy($this->config);
        $response = $subscriber->subscribe([
            'name' => 'Alison',
            'email' => 'alison@gmail.com',
        ]);

        $this->assertEquals(true, $response['status']);
        $this->assertEquals('Subscribed.', $response['message']);
    }

    public function testSubscribeASubscriberThatAlreadyExists()
    {
        $subscriber = new Sendy($this->config);
        $subscriber->subscribe([
            'name' => 'Alison',
            'email' => 'alison2@gmail.com',
        ]);

        $response = $subscriber->subscribe([
            'name' => 'Alison',
            'email' => 'alison2@gmail.com',
        ]);

        $this->assertEquals(false, $response['status']);
        $this->assertEquals('Already subscribed.', $response['message']);
    }

    public function testSimpleUnsubscribe()
    {
        $subscriber = new Sendy($this->config);
        $response = $subscriber->unsubscribe('alison2@gmail.com');

        $this->assertEquals(true, $response['status']);
        $this->assertEquals('Unsubscribed', $response['message']);
    }

    public function testUnsubscribeASubscriberThatNotExists()
    {
        $subscriber = new Sendy($this->config);
        $response = $subscriber->unsubscribe('zzzz@gmail.com');

        // The API doesn't provide this type of error
        $this->assertEquals(true, $response['status']);
        $this->assertEquals('Unsubscribed', $response['message']);
    }

    public function testCheckStatus()
    {
        $subscriber = new Sendy($this->config);

        $response1 = $subscriber->subscriptionStatus('zzzz@gmail.com');

        $this->assertEquals('Email does not exist in list', $response1['message']);

        $response2 = $subscriber->subscriptionStatus('alison2@gmail.com');

        $this->assertEquals('Unsubscribed', $response2['message']);

        $response3 = $subscriber->subscriptionStatus('alison@gmail.com');

        $this->assertEquals('Subscribed', $response3['message']);
    }

    public function testUpdate()
    {
        $subscriber = new Sendy($this->config);
        $response = $subscriber->update('alison@gmail.com', [
            'name' => 'Alison 2',
        ]);

        // This method use `subscribe` method to update data
        $this->assertEquals(true, $response['status']);
        $this->assertEquals('Already subscribed.', $response['message']);
    }
}
