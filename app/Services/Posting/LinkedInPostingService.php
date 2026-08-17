<?php

namespace App\Services\Posting;

use App\Models\ChannelSetting;
use App\Models\ScheduledPost;
use Illuminate\Support\Facades\Http;

class LinkedInPostingService implements PostingService
{
    public function post(ScheduledPost $post, ChannelSetting $channel): string
    {
        $authorUrn = "urn:li:person:{$channel->external_account_id}";
        $text = trim($post->caption . "\n\n" . $post->hashtags);

        $response = Http::withToken($channel->access_token)
            ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
            ->post('https://api.linkedin.com/v2/ugcPosts', [
                'author' => $authorUrn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => ['text' => $text],
                        'shareMediaCategory' => 'NONE',
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException("LinkedIn post failed: {$response->body()}");
        }

        // LinkedIn returns the post URN in a response header, not the body.
        $postId = $response->header('x-restli-id');

        if (! $postId) {
            throw new \RuntimeException('LinkedIn post succeeded but returned no post ID header.');
        }

        return $postId;
    }
}
