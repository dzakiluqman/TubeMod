<?php

class YoutubeModel {

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

    public function fetchComments($videoId) {

        $apiKey = "AIzaSyAyrBH68PIuQFuWADOSyEXFdpqajRPA_JE";
        $comments = [];

        $apiUrl = "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&videoId=$videoId&maxResults=20&key=$apiKey";
        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $comments[] = [
                    'author' => $item['snippet']['topLevelComment']['snippet']['authorDisplayName'],
                    'text' => $item['snippet']['topLevelComment']['snippet']['textDisplay']
                ];
            }
        }

        return $comments;
    }
}