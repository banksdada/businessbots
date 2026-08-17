<?php

namespace App\Services\Posting;

use App\Models\ChannelSetting;
use App\Models\ScheduledPost;
use Illuminate\Support\Facades\Http;

class InstagramPostingService implements PostingService
{
    /**
     * Instagram's Graph API posts in two steps: create a media container,
     * then publish it. A container that fails to publish (still processing,
     * rate limited) needs a short poll before giving up — handled inline
     * here since it's specific to this one platform's API shape.
     */
    public function post(ScheduledPost $post, ChannelSetting $channel): string
    {
        $igUserId = $channel->external_account_id;
        $caption = trim($post->caption . "\n\n" . $post->hashtags);

        $containerResponse = Http::withToken($channel->access_token)
            ->post("https://graph.facebook.com/v19.0/{$igUserId}/media", [
                'caption' => $caption,
                'image_url' => $post->media_url ?? null, // null = text-only not supported by IG; see note below
            ]);

        if ($containerResponse->failed()) {
            throw new \RuntimeException("Instagram container creation failed: {$containerResponse->body()}");
        }

        $containerId = $containerResponse->json('id');

        if (! $containerId) {
            throw new \RuntimeException('Instagram container creation returned no ID.');
        }

        $this->waitForContainerReady($containerId, $channel->access_token);

        $publishResponse = Http::withToken($channel->access_token)
            ->post("https://graph.facebook.com/v19.0/{$igUserId}/media_publish", [
                'creation_id' => $containerId,
            ]);

        if ($publishResponse->failed()) {
            throw new \RuntimeException("Instagram publish failed: {$publishResponse->body()}");
        }

        $postId = $publishResponse->json('id');

        if (! $postId) {
            throw new \RuntimeException('Instagram publish returned no post ID.');
        }

        return $postId;
    }

    /**
     * Container processing is usually instant but occasionally takes a few
     * seconds — poll briefly rather than assuming ready immediately, which
     * is the single most common cause of "publish failed" on IG's API.
     */
    private function waitForContainerReady(string $containerId, string $accessToken, int $maxAttempts = 5): void
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $status = Http::withToken($accessToken)
                ->get("https://graph.facebook.com/v19.0/{$containerId}", ['fields' => 'status_code'])
                ->json('status_code');

            if ($status === 'FINISHED') {
                return;
            }

            if ($status === 'ERROR') {
                throw new \RuntimeException("Instagram container {$containerId} failed processing.");
            }

            usleep(500_000); // 0.5s between polls — total worst case ~2.5s added to job runtime
        }

        // Not fatal — attempt publish anyway; Meta's API often accepts it even
        // mid-"IN_PROGRESS", and the real failure (if any) surfaces at publish.
    }
}
