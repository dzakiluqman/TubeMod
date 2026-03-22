<?php

class YoutubeModel {

    private $apiKey = "AIzaSyAyrBH68PIuQFuWADOSyEXFdpqajRPA_JE";

    public function getVideoId($url) {

        parse_str(parse_url($url, PHP_URL_QUERY), $params);

        if (isset($params['v'])) {
            return $params['v'];
        }

        $path = parse_url($url, PHP_URL_PATH);

        if ($path) {
            return trim($path, '/');
        }

        return null;
    }

    public function getComments($url) {

        $videoId = $this->getVideoId($url);

        if (!$videoId) {
            return [];
        }

        $apiUrl = "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&videoId=$videoId&maxResults=20&key={$this->apiKey}";

        $response = file_get_contents($apiUrl);

        if (!$response) {
            return [];
        }

        $data = json_decode($response, true);

        $comments = [];

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $comments[] = [
                    'id' => $item['snippet']['topLevelComment']['id'],
                    'author' => $item['snippet']['topLevelComment']['snippet']['authorDisplayName'],
                    'text' => $item['snippet']['topLevelComment']['snippet']['textDisplay']
                ];
            }
        }

        return $comments;
    }

    public function isVideoOwner($videoId, $accessToken) {

    $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=$videoId";

    $opts = [
        "http" => [
            "header" => "Authorization: Bearer $accessToken"
        ]
    ];

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    $data = json_decode($response, true);

    if (!isset($data['items'][0])) return false;

    $channelId = $data['items'][0]['snippet']['channelId'];

    // bandingkan dengan channel user
    return $channelId === $_SESSION['youtube_channel_id'];
    }

    public function deleteComment($commentId, $accessToken)
    {
        $url = "https://www.googleapis.com/youtube/v3/comments?id=$commentId";

        $opts = [
            "http" => [
                "method" => "DELETE",
                "header" => "Authorization: Bearer $accessToken"
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        return $response !== false;
    }
}