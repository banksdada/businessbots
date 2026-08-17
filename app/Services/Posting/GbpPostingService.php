<?php

namespace App\Services\Posting;

use App\Models\ChannelSetting;
use App\Models\ScheduledPost;
use Illuminate\Support\Facades\Http;

class GbpPostingService implements PostingService
{
    /**
     * Google Business Profile's API addresses posts by account+location
     * resource path, not a flat "user ID" like Instagram/LinkedIn. That path
     * is resolved and stored in external_account_id at connect time — see
     * ChannelOAuthController::resolveGbpLocation(). Note: only the first
     * location on the account is used; a business with multiple GBP
     * locations has no way to pick which one connects (see that method's
     * docblock for the same limitation).
     */
    public function post(ScheduledPost $post, ChannelSetting $channel): string
    {
        $locationPath = $channel->external_account_id; // expected shape: accounts/{accountId}/locations/{locationId}
        $text = trim($post->caption . "\n\n" . $post->hashtags);

        $response = Http::withToken($channel->access_token)
            ->post("https://mybusiness.googleapis.com/v4/{$locationPath}/localPosts", [
                'languageCode' => 'en-GB',
                'summary' => $text,
                'topicType' => 'STANDARD',
            ]);

        if ($response->failed()) {
            throw new \RuntimeException("Google Business Profile post failed: {$response->body()}");
        }

        $postName = $response->json('name'); // e.g. "accounts/.../locations/.../localPosts/{postId}"

        if (! $postName) {
            throw new \RuntimeException('GBP post succeeded but returned no post name.');
        }

        return $postName;
    }
}
