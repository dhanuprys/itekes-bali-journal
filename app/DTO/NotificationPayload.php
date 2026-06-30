<?php

namespace App\DTO;

class NotificationPayload
{
    public function __construct(
        public string $title,
        public ?string $url = null,
        public ?string $type = null, // e.g., 'info', 'success', 'warning', 'error'
        public ?array $metadata = null
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'url' => $this->url,
            'type' => $this->type,
            'metadata' => $this->metadata,
        ], fn($value) => !is_null($value));
    }
}
