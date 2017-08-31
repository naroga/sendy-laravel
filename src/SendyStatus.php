<?php

namespace BuddyAd\Sendy;

/**
 * Class SendyStatus
 *
 * @package BuddyAd\Sendy
 */
abstract class SendyStatus
{
    const ST_SUCCESS = [
        '1' => 100,
        'Bounced' => 101,
        'Campaign created' => 102,
        'Campaign created and now sending' => 103,
        'Complained' => 104,
        'Soft bounced' => 105,
        'Subscribed' => 106,
        'Unconfirmed' => 107,
        'Unsubscribed' => 108,
    ];

    const ST_ERROR = [
        'Already subscribed.' => 201,
        'API key not passed' => 202,
        'Brand ID not passed' => 203,
        'Email address not passed' => 204,
        'Email does not exist in list' => 205,
        'Email not passed' => 206,
        'From email not passed' => 207,
        'From name not passed' => 208,
        'HTML not passed' => 209,
        'Invalid API key' => 210,
        'Invalid email address.' => 211,
        'Invalid list ID.' => 212,
        'List does not exist' => 213,
        'List ID not passed' => 214,
        'List ID(s) not passed' => 215,
        'List IDs does not belong to a single brand' => 216,
        'No data passed' => 217,
        'One or more list IDs are invalid' => 218,
        'Reply to email not passed' => 219,
        'Some fields are missing.' => 220,
        'Subject not passed' => 221,
        'Subscriber does not exist' => 222,
        'Unable to create and send campaign' => 223,
        'Unable to create campaign' => 224,
    ];
}
