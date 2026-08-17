<?php

namespace App\Console\Commands;

use App\Models\ScheduledPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupGeneratedImages extends Command
{
    protected $signature = 'posts:cleanup-images';
    protected $description = 'Delete generated post images past their retention window, freeing disk space';

    /**
     * Two different retention windows, because "posted and doing fine" and
     * "never got posted" mean different things:
     *   - Successfully posted: keep the image 90 days (matches the data
     *     retention period in the Privacy Policy) in case a business wants
     *     to reference or re-check what actually went out.
     *   - Never posted (post generation succeeded but publishing never
     *     happened — channel disconnected, business went inactive, etc.):
     *     keep only 7 days. There's no reason to hold onto an image for
     *     content that's never going anywhere.
     */
    private const POSTED_RETENTION_DAYS = 90;
    private const UNPOSTED_RETENTION_DAYS = 7;

    public function handle(): int
    {
        $deletedFiles = 0;
        $missingFiles = 0;

        $deletedFiles += $this->cleanupBatch(
            ScheduledPost::whereNotNull('media_url')
                ->whereNotNull('posted_at')
                ->where('posted_at', '<=', now()->subDays(self::POSTED_RETENTION_DAYS)),
            $missingFiles,
        );

        $deletedFiles += $this->cleanupBatch(
            ScheduledPost::whereNotNull('media_url')
                ->whereNull('posted_at')
                ->where('created_at', '<=', now()->subDays(self::UNPOSTED_RETENTION_DAYS)),
            $missingFiles,
        );

        $this->info("Deleted {$deletedFiles} image file(s). {$missingFiles} record(s) already had no file on disk.");

        if ($missingFiles > 0) {
            // Not necessarily a bug — could mean a previous cleanup run partially
            // completed, or the shared volume had an issue. Worth knowing about,
            // not worth failing the whole command over.
            \Log::info('[CleanupGeneratedImages] some media_url records had no matching file', ['count' => $missingFiles]);
        }

        return self::SUCCESS;
    }

    private function cleanupBatch($query, int &$missingFiles): int
    {
        $deleted = 0;

        $query->chunkById(100, function ($posts) use (&$deleted, &$missingFiles) {
            foreach ($posts as $post) {
                $path = $this->pathFromMediaUrl($post->media_url);

                if ($path === null) {
                    continue; // media_url didn't match our own storage URL shape — leave it alone, don't guess
                }

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $deleted++;
                } else {
                    $missingFiles++;
                }

                // Clear media_url so this row is never picked up by future
                // cleanup runs again, and so it's visually obvious in
                // Filament/analytics that the image no longer exists.
                $post->update(['media_url' => null]);
            }
        });

        return $deleted;
    }

    /**
     * media_url is stored as a full public URL (e.g.
     * https://automation.baseuse.xyz/storage/generated-posts/{uuid}.png —
     * see content_generator.py _generate_image()). Storage::delete() needs
     * the relative disk path instead, so this converts one to the other
     * rather than trusting the URL's domain (which could differ between
     * environments) or assuming a fixed prefix without checking.
     */
    private function pathFromMediaUrl(string $mediaUrl): ?string
    {
        $marker = '/storage/';
        $position = strpos($mediaUrl, $marker);

        if ($position === false) {
            return null;
        }

        return substr($mediaUrl, $position + strlen($marker));
    }
}
