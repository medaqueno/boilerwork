#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Kernel\Domain\AggregateHistory;
use Kernel\Domain\RecordsEvents;
use Kernel\Domain\ValueObjects\Identity;
use Kernel\Infra\Persistence\EventStore;
use Kernel\System\Clients\RedisClient;
use Redis;

final class UserRedisRepository implements UserRepository
{
    private Redis $redis;
    private $client;

    public function __construct()
    {
        $this->client = RedisClient::getInstance();
        $this->redis = $this->client->getConn();
    }

    public function append(RecordsEvents $aggregate): void
    {
        $events = $aggregate->getRecordedEvents();

        foreach ($events as $event) {
            $this->redis->hSet($event->getAggregateId()->toPrimitive(), $event::class, serialize($event));
        }

        $aggregate->clearRecordedEvents();

        $this->client->putConn($this->redis);
    }

    public function getAggregateHistoryFor(Identity $id): RecordsEvents
    {
        // Query events by ID
        $data = $this->redis->hGetAll($id->toPrimitive());
        $this->client->putConn($this->redis);

        $events = [];
        foreach ($data as $event) {
            $events[] = unserialize($event);
        }

        // var_dump($events);
        return User::reconstituteFrom(
            new AggregateHistory(
                $id,
                $events
            )
        );
    }
    /*
    public function save(Post $aPost)
    {
    }

    public function remove(Post $aPost)
    {
        $this->redis->hdel('posts', (string) $aPost->id());
    }

    public function postOfId(PostId $anId)
    {
        if ($data = $this->redis->hget('posts', (string) $anId)) {
            return unserialize($data);
        }
        return null;
    }

    public function latestPosts(\DateTime $sinceADate)
    {
        $latest = $this->filterPosts(
            function (Post $post) use ($sinceADate) {
                return $post->createdAt() > $sinceADate;
            }
        );

        $this->sortByCreatedAt($latest);
        return array_values($latest);
    }

    private function filterPosts(callable $fn)
    {
        return array_filter(array_map(function ($data) {
            return unserialize($data);
        }, $this->redis->hgetall('posts')), $fn);
    }

    private function sortByCreatedAt(&$posts)
    {
        usort($posts, function (Post $a, Post $b) {
            if ($a->createdAt() == $b->createdAt()) {
                return 0;
            }
            return ($a->createdAt() < $b->createdAt()) ? -1 : 1;
        });
    }
    public function nextIdentity()
    {
        return new PostId();
    }
    */
}
