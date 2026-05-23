<?php

class YoutubeModel {

    private $apiKey;

    public function __construct() {
        $this->apiKey = $_ENV['YOUTUBE_API_KEY'];
    }

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

    public function getComments($url)
    {
        $videoId = $this->getVideoId($url);

        if (!$videoId) {
            return [];
        }

        $apiUrl = "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&videoId=$videoId&maxResults=20&key={$this->apiKey}";

        $response = file_get_contents($apiUrl);
        if (!$response) return [];

        $data = json_decode($response, true);
        $comments = [];

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {

                $top = $item['snippet']['topLevelComment'];

                $commentId = $top['id'];

                $comments[] = [
                    'id' => $commentId,
                    'author' => $top['snippet']['authorDisplayName'],
                    'text' => $top['snippet']['textDisplay']
                ];
            }
        }

        return $comments;
    }

    public function isVideoOwner($videoId, $accessToken)
    {
        $urlChannel = "https://www.googleapis.com/youtube/v3/channels?part=id&mine=true";

        $opts = [
            "http" => [
                "header" => "Authorization: Bearer $accessToken",
                "ignore_errors" => true
            ]
        ];

        $context = stream_context_create($opts);
        $resChannel = file_get_contents($urlChannel, false, $context);

        // handle error token
        if (!$resChannel || strpos($http_response_header[0], '401') !== false) {
            return false;
        }

        $dataChannel = json_decode($resChannel, true);

        if (!isset($dataChannel['items'][0]['id'])) return false;

        $myChannelId = $dataChannel['items'][0]['id'];

        // ambil channel pemilik video
        $urlVideo = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=$videoId&key={$this->apiKey}";
        $resVideo = file_get_contents($urlVideo);
        $dataVideo = json_decode($resVideo, true);

        if (!isset($dataVideo['items'][0]['snippet']['channelId'])) return false;

        $videoChannelId = $dataVideo['items'][0]['snippet']['channelId'];

        return $myChannelId === $videoChannelId;
    }

    public function deleteComment($commentId, $accessToken)
    {
        $commentId = trim($commentId);

        $url = "https://www.googleapis.com/youtube/v3/comments?id=$commentId";

        $opts = [
            "http" => [
                "method" => "DELETE",
                "header" => "Authorization: Bearer $accessToken",
                "ignore_errors" => true
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        // cek status
        if (isset($http_response_header[0]) && strpos($http_response_header[0], '204') !== false) {
            return true; // sukses delete
        }

        return $this->hideComment($commentId, $accessToken);
    }

    public function hideComment($commentId, $accessToken)
    {
        $url = "https://www.googleapis.com/youtube/v3/comments/setModerationStatus";

        $data = [
            "id" => $commentId,
            "moderationStatus" => "rejected"
        ];

        $opts = [
            "http" => [
                "method" => "POST",
                "header" => "Authorization: Bearer $accessToken\r\nContent-Type: application/json",
                "content" => json_encode($data),
                "ignore_errors" => true
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        if (isset($http_response_header[0]) && strpos($http_response_header[0], '200') !== false) {
            return true;
        }

        return false;
    }

    public function getVideoTitle($videoId)
    {
        $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=$videoId&key={$this->apiKey}";

        $response = file_get_contents($url);

        if (!$response) {
            return 'Unknown Title';
        }

        $data = json_decode($response, true);

        return $data['items'][0]['snippet']['title'] ?? 'Unknown Title';
    }
}