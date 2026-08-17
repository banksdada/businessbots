<?php

namespace App\Services\Posting;

use App\Models\ChannelSetting;
use App\Models\ScheduledPost;

interface PostingService
{
    /**
     * Publish the post to the platform. Returns the platform's own post ID
     * on success — stored in scheduled_posts.post_id so analytics_collector.py
     * (Python side) knows what to fetch metrics for later.
     *
     * @throws \RuntimeException on failure — caller (PostToChannelJob) handles
     *         retry/backoff and logging; this layer just reports what happened.
     */
    public function post(ScheduledPost $post, ChannelSetting $channel): string;
}
