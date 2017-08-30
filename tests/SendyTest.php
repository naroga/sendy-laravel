<?php

namespace BuddyAd\Tests\Sendy;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

use BuddyAd\Sendy\Sendy;

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
        $subscriber = $subscriber->subscribe([
            'name' => 'Alison',
            'email' => 'alison@gmail.com',
        ]);

        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Subscribed.', $subscriber['message']);
    }

    public function testSubscribeASubscriberThatAlreadyExists()
    {
        $subscriber = new Sendy($this->config);
        $subscriber->subscribe([
            'name' => 'Alison',
            'email' => 'alison2@gmail.com',
        ]);

        $subscriber = $subscriber->subscribe([
            'name' => 'Alison',
            'email' => 'alison2@gmail.com',
        ]);

        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Already subscribed.', $subscriber['message']);
    }

    public function testSimpleUnsubscribe()
    {
        $subscriber = new Sendy($this->config);
        $subscriber = $subscriber->unsubscribe('alison2@gmail.com');

        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Unsubscribed', $subscriber['message']);
    }

    public function testUnsubscribeASubscriberThatNotExists()
    {
        $subscriber = new Sendy($this->config);
        $subscriber = $subscriber->unsubscribe('zzzz@gmail.com');

        // The API doesn't provide this type of error
        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Unsubscribed', $subscriber['message']);
    }

    public function testCheckStatus()
    {
        $subscriber = new Sendy($this->config);

        $subscriber1 = $subscriber->status('zzzz@gmail.com');

        $this->assertEquals('Email does not exist in list', $subscriber1);

        $subscriber2 = $subscriber->status('alison2@gmail.com');

        $this->assertEquals('Unsubscribed', $subscriber2);

        $subscriber3 = $subscriber->status('alison@gmail.com');

        $this->assertEquals('Subscribed', $subscriber3);
    }

    public function testUpdate()
    {
        $subscriber = new Sendy($this->config);
        $subscriber = $subscriber->update('alison@gmail.com', [
            'name' => 'Alison 2',
        ]);

        // This method use `subscribe` method to update data
        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Already subscribed.', $subscriber['message']);
    }
}
