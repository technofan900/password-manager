<?php
namespace Core;

class RateLimiter
{
    protected string $storagePath;

    public function __construct(string $storagePath)
    {
        $this->storagePath = rtrim($storagePath, DIRECTORY_SEPARATOR);
    }

    public function attempt(string $key, int $maxAttempts, int $decaySeconds)
    {
        if (! is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }

        $file = $this->storagePath . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.json';
        $now = time();
        $data = [
            'attempts' => 0,
            'expires_at' => $now + $decaySeconds
        ];

        $handle = fopen($file, 'c+');

        if (! $handle) {
            return [
                'allowed' => true,
                'remaining' => $maxAttempts,
                'retry_after' => 0
            ];
        }

        flock($handle, LOCK_EX);

        $contents = stream_get_contents($handle);
        $stored = $contents ? json_decode($contents, true) : null;

        if (is_array($stored) && ($stored['expires_at'] ?? 0) > $now) {
            $data = $stored;
        }

        $data['attempts']++;
        $allowed = $data['attempts'] <= $maxAttempts;
        $retryAfter = max(0, $data['expires_at'] - $now);

        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, json_encode($data));
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        return [
            'allowed' => $allowed,
            'remaining' => max(0, $maxAttempts - $data['attempts']),
            'retry_after' => $allowed ? 0 : $retryAfter
        ];
    }
}
