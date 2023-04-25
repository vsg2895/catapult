<?php

namespace App\Contracts;

interface TwitterServiceContract
{
    public function user(string $name);
    public function tweet(string $id);
    public function space(string $name);
    public function userTweets(string $id);
    public function userFollowers(string $id);
    public function tweetLikes(string $id);
    public function tweetReplies(string $id);
    public function tweetRetweets(string $id);
}
