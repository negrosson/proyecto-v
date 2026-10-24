<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class ContactMessageData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $eventAddress,
        public ?string $eventDate,
        public string $message,
        public ?string $ipAddress = null,
    ) {}
}
