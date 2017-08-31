<?php

namespace BuddyAd\Sendy;

use BuddyAd\Sendy\Exceptions\SendyException;

/**
 * Class Sendy
 *
 * @package BuddyAd\Sendy
 */
class Sendy extends SendyStatus
{
    protected $config;
    protected $installationUrl;
    protected $apiKey;
    protected $listId;

    /**
     * Sendy constructor.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->setListId($config['listId']);
        $this->setInstallationUrl($config['installationUrl']);
        $this->setApiKey($config['apiKey']);
        $this->checkProperties();
    }

    /**
     * @param mixed $installationUrl
     */
    public function setInstallationUrl($installationUrl)
    {
        $this->installationUrl = $installationUrl;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param $listId
     *
     * @return $this
     */
    public function setListId($listId)
    {
        $this->listId = $listId;

        return $this;
    }

    /**
     * Method to add a new subscriber to a list
     *
     * @param array $values
     *
     * @return array
     */
    public function subscribe(array $values): array
    {
        $response = $this->buildAndSend('subscribe', $values);
        $notice = $this->buildResponse($response);

        if ($notice['message'] === '1') {
            $notice['message'] = 'Subscribed.';
        }

        return $notice;
    }

    /**
     * Updating a subscriber using the email like a reference/key
     * If the email doesn't exists in the current list, this will create a new subscriber
     *
     * @param $email
     * @param array $values
     *
     * @return array
     */
    public function update($email, array $values): array
    {
        $values = array_merge([
            'email' => $email
        ], $values);
        $notice = $this->subscribe($values);
        $notice['status'] = !$notice['status'];

        return $notice;
    }

    /**
     * Method to unsubscribe a user from a list
     *
     * @param $email
     *
     * @return array
     */
    public function unsubscribe($email): array
    {
        $response = $this->buildAndSend('unsubscribe', [
            'email' => $email,
        ]);
        $notice = $this->buildResponse($response);

        if ($notice['message'] === '1') {
            $notice['message'] = 'Unsubscribed';
        }

        return $notice;
    }

    /**
     * Method to get the current status of a subscriber.
     * Success: Subscribed, Unsubscribed, Unconfirmed, Bounced, Soft bounced, Complained
     * Error: No data passed, Email does not exist in list, etc.
     *
     * @param string $email
     *
     * @return array
     */
    public function subscriptionStatus(string $email): array
    {
        $url = 'api/subscribers/subscription-status.php';
        $response = $this->buildAndSend($url, [
            'email' => $email,
        ]);

        return $this->buildResponse($response);
    }

    /**
     * Gets the total active subscriber count
     *
     * @return array
     */
    public function count(): array
    {
        $url = 'api/subscribers/active-subscriber-count.php';
        $response = $this->buildAndSend($url, []);

        return $this->buildResponse($response);
    }

    /**
     * Create a campaign based on the input params. See API (https://sendy.co/api#4) for parameters.
     * Bug: The API doesn't save the listIds passed to Sendy.
     *
     * @param $options
     * @param $content
     * @param bool $send : Set this to true to send the campaign
     *
     * @return array
     *
     * @throws \Exception
     */
    public function createCampaign($options, $content, $send = false): array
    {
        $url = '/api/campaigns/create.php';

        if (empty($options['from_name'])) {
            throw new SendyException('From Name is not set');
        }

        if (empty($options['from_email'])) {
            throw new SendyException('From Email is not set');
        }

        if (empty($options['reply_to'])) {
            throw new SendyException('Reply To address is not set');
        }

        if (empty($options['subject'])) {
            throw new SendyException('Subject is not set');
        }

        // 'plain_text' field can be included, but optional
        if (empty($content['html_text'])) {
            throw new SendyException('Campaign Content (HTML) is not set');
        }

        if ($send && empty($options['brand_id'])) {
            throw new SendyException('Brand ID should be set for Draft campaigns');
        }

        // list IDs can be single or comma separated values
        if (empty($options['list_ids'])) {
            $options['list_ids'] = $this->listId;
        }

        // should we send the campaign (1) or save as Draft (0)
        $options['send_campaign'] = $send ? 1 : 0;

        $response = $this->buildAndSend($url, array_merge($options, $content));

        return $this->buildResponse($response);
    }

    /**
     * @param $url
     * @param array $values
     *
     * @return string
     */
    private function buildAndSend($url, array $values): string
    {
        /**
         * Merge the passed in values with the options for return
         * Passing listId too, because old API calls use list, new ones use listId
         */
        $content = array_merge($values, [
            'list' => $this->listId,
            'list_id' => $this->listId, # ¯\_(ツ)_/¯
            'api_key' => $this->apiKey,
            'boolean' => 'true',
        ]);

        /**
         * Build a query using the $content
         */
        $post_data = http_build_query($content);
        $ch = curl_init($this->installationUrl . '/' . $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Checks the properties
     *
     * @throws \Exception
     */
    private function checkProperties()
    {
        if (null === $this->listId) {
            throw new SendyException('[listId] is not set');
        }

        if (null === $this->installationUrl) {
            throw new SendyException('[installationUrl] is not set');
        }

        if (null === $this->apiKey) {
            throw new SendyException('[apiKey] is not set');
        }
    }

    /**
     * @param $response
     *
     * @return bool
     */
    private function isError($response): bool
    {
        return !isset(self::ST_ERROR[$response]);
    }

    /**
     * @param $response
     *
     * @return array
     */
    private function buildResponse($response): array
    {
        $notice = [
            'status' => $this->isError($response),
            'message' => $response,
        ];

        return $notice;
    }
}
