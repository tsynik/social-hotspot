<?php

session_start();

require 'vendor/autoload.php';
require 'config.php';

$user = null;
$liked = null;

$facebook = new Facebook(
    array(
        'appId' => APP_ID,
        'secret' => APP_SECRET,
        'cookie' => true
    )
);

$user = $facebook->getUser();

$loginUrl = $facebook->getLoginUrl(
    array(
        'scope' => 'publish_stream, user_likes',
        'redirect_uri' => BASE_URI
    )
);

if ($user) {
    try {
        $likes = $facebook->api('/me/likes' . PAGE_ID);

        if (!empty($likes['date'])) {
            $liked = true;
        } else {
            $liked = false;
        }
    } catch (FacebookApiException $e) {
        print_r($e);
    }

    if (isset($_GET['code'])) {
        header('Location: ' . BASE_URI);
        exit;
    }

    if (isset($_GET['checkin'])) {
        try {
            $status = $facebook->api("$user/feed", 'post',
                array(
                    'message' => MESSAGE,
                    'place' => PAGE_ID
                )
            );
        } catch (FacebookApiException $e) {
            print_r($e);
        }

        if(!isset($_SESSION['shared']))
            $_SESSION['shared'] = true;

        header('Location: ' . BASE_URI);
        exit;
    }
}