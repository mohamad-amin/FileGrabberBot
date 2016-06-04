<?php

/**
 * Created by PhpStorm.
 * User: Mohamad Amin
 * Date: 3/26/2016
 * Time: 12:58 AM
 */

require __DIR__ . '/vendor/autoload.php';
use Longman\TelegramBot\Telegram;

ignore_user_abort(true);//if caller closes the connection (if initiating with cURL from another PHP, this allows you to end the calling PHP script without ending this one)
set_time_limit(0);

$hLock=fopen(__FILE__.".lock", "w+");
if(!flock($hLock, LOCK_EX | LOCK_NB))
    die("Already running. Exiting...");

$API_KEY = '211996742:AAEnbgUBo4dBkjMfAy7usLwe_WNbo_7nGH4';
$BOT_NAME = 'FileGrabberBot';

$telegram = null;

try {
    // Create Telegram API object
    $telegram = new Telegram($API_KEY, $BOT_NAME);
    // Enable MySQL
    // Handle telegram getUpdate request
    $telegram->addCommandsPath('commands');
    $telegram->setDownloadPath('file');
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    echo $e;
}

try {
    while (true) {
        $telegram->handleGetUpdates();
        sleep(1);
    }
} catch (\Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}

flock($hLock, LOCK_UN);
fclose($hLock);
unlink(__FILE__.".lock");