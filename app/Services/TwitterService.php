<?php

namespace App\Services;

use App\Contracts\TwitterServiceContract;

use Atymic\Twitter\Facade\Twitter;
use Illuminate\Support\Facades\Cache;
use Atymic\Twitter\Exception\ClientException;

class TwitterService implements TwitterServiceContract
{
    public function user(string $name)
    {
        return Cache::remember(sprintf('twitter_user_%s', $name), 3600, function () use ($name) {
            try {
                $response = Twitter::getUserByUsername($name, [
                    'user.fields' => 'created_at,profile_image_url',
                    'response_format' => 'array',
                ]);

                if (!isset($response['data']) || isset($response['errors'])) {
                    $response = [
                        'data' => [],
                    ];
                }
            } catch (ClientException) {
                $response = [
                    'data' => [],
                ];
            }

            return $response['data'];
        });
    }

    public function tweet(string $id)
    {
        return Cache::remember(sprintf('twitter_tweet_%s', $id), 3600, function () use ($id) {
            try {
                $response = Twitter::getTweet($id, [
                    'expansions' => 'author_id',
                    'user.fields' => 'created_at,profile_image_url',
                    'tweet.fields' => 'author_id,conversation_id,created_at,edit_history_tweet_ids,lang,text',
                    'response_format' => 'array',
                ]);

                if (isset($response['includes']['users'])) {
                    $response['data']['user'] = $response['includes']['users'][0];
                }

                if (!isset($response['data']) || isset($response['errors'])) {
                    $response = [
                        'data' => [],
                    ];
                }
            } catch (ClientException) {
                $response = [
                    'data' => [],
                ];
            }

            return $response['data'];
        });
    }

    public function space(string $name)
    {
        try {
            $response = Twitter::forApiV2()->getQuerier()->withOAuth2Client()->get(sprintf('spaces/%s', $name), [
                'expansions' => 'creator_id',
                'space.fields' => 'creator_id,host_ids,title,state,created_at,updated_at,lang,participant_count',
                'response_format' => 'array',
            ]);

            if (array_key_exists('includes', $response)
                && array_key_exists('users', $response['includes'])) {
                $response['data']['users'] = $response['includes']['users'];
            }

            if (!isset($response['data']) || isset($response['errors'])) {
                $response = [
                    'data' => [],
                ];
            }
        } catch (ClientException) {
            $response = [
                'data' => [],
            ];
        }

        return $response['data'];
    }

    public function userTweets(string $id)
    {
        try {
            $response = Twitter::forApiV2()->getQuerier()->withOAuth2Client()->get(sprintf('users/%s/tweets', $id), [
                'response_format' => 'array',
            ]);

            if (!isset($response['data']) || isset($response['errors'])) {
                $response = [
                    'data' => [],
                ];
            }
        } catch (ClientException) {
            $response = [
                'data' => [],
            ];
        }

        return $response['data'];
    }

    public function userFollowers(string $id)
    {
        try {
            $response = Twitter::getFollowers($id, [
                'response_format' => 'array',
            ]);

            if (!isset($response['data']) || isset($response['errors'])) {
                $response = [
                    'data' => [],
                ];
            }
        } catch (ClientException) {
            $response = [
                'data' => [],
            ];
        }

        return $response['data'];
    }

    public function tweetLikes(string $id)
    {
        try {
            $response = Twitter::forApiV2()->getQuerier()->withOAuth2Client()->get(sprintf('tweets/%s/liking_users', $id), [
                'response_format' => 'array',
            ]);

            if (!isset($response['data']) || isset($response['errors'])) {
                $response = [
                    'data' => [],
                ];
            }
        } catch (ClientException) {
            $response = [
                'data' => [],
            ];
        }

        return $response['data'];
    }

    public function tweetReplies(string $id)
    {
        try {
            $response = Twitter::forApiV2()->getQuerier()->withOAuth2Client()->get('tweets/search/recent', [
                'query' => sprintf('conversation_id:%s', $id),
                'max_results' => 100,
                'tweet.fields' => 'author_id',
                'response_format' => 'array',
            ]);

            if (!isset($response['data']) || isset($response['errors'])) {
                $response = [
                    'data' => [],
                ];
            }
        } catch (ClientException) {
            $response = [
                'data' => [],
            ];
        }

        return $response['data'];
    }

    public function tweetRetweets(string $id)
    {
        try {
            $response = Twitter::forApiV2()->getQuerier()->withOAuth2Client()->get(sprintf('tweets/%s/retweeted_by', $id), [
                'response_format' => 'array',
            ]);

            if (!isset($response['data']) || isset($response['errors'])) {
                $response = [
                    'data' => [],
                ];
            }
        } catch (ClientException) {
            $response = [
                'data' => [],
            ];
        }

        return $response['data'];
    }
}
