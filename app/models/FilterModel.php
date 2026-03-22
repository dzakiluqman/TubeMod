<?php

class FilterModel {

    public function filterComments($comments, $keywords)
    {
        $filtered = [];

        foreach ($comments as $comment) {

            foreach ($keywords as $keyword) {

                if (stripos($comment['text'], $keyword['word']) !== false) {

                    $comment['matched_keyword'] = $keyword['word'];
                    $comment['category'] = $keyword['category'];

                    $filtered[] = $comment;
                    break;
                }
            }
        }

        return $filtered;
    }
}