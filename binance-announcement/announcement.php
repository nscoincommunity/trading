<?php

/**
 * @Author : Said Thitah
 */

//telegram data (change it if you want a production version)
$token = "bot_token";
$channelId = "@channel_id";
$user_id = "@user_id";
$fileName = "lastAnnouncement.txt";


//shorten service data (change it if you want a production version)
$shorten_token = "shorten_token";

echo "let's begin the work !";

while (true) {
    $announcement = @file_get_contents("https://www.binance.com/bapi/composite/v1/public/cms/article/catalog/list/query?catalogId=48&pageNo=1&pageSize=3");
    $announcement = json_decode($announcement);
    $lastAnnouncement = $announcement->data->articles[0];

    $id = $lastAnnouncement->code;

    if (!file_exists($fileName)) {
        file_put_contents($fileName, $id);
    }
    $ids = file($fileName, FILE_IGNORE_NEW_LINES);
    if (!in_array($id, $ids)) {
        $ids = [$id, $ids[0]];
        $title = "📢" . $lastAnnouncement->title;
        $body = $title . "\nFor more details click on the link bellow \n\n";
        $body .= "🔗" . shorten("https://www.binance.com/en/support/announcement/" . $id);
        telegram($token, $user_id, $body);
        file_put_contents($fileName, implode("\n", $ids));
    }
    sleep(60);
}


function shorten($url)
{
    global $shorten_token;

    $data = "urlToShorten=" . urlencode($url);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.shorte.st/v1/data/url');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $headers = array();
    $headers[] = 'Public-Api-Token: ' . $shorten_token;
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);


    $response = json_decode($response);
    if (!$response || strcmp($response->status, "ok") !== 0) {
        return $url;
    }
    return $response->shortenedUrl;

}

function telegram($token, $userId, $message)
{
    $postData = array(
        "chat_id" => $userId,
        "text" => $message
    );

    $url = "https://api.telegram.org/bot" . $token . "/sendMessage?" . http_build_query($postData);
    return file_get_contents($url);
}