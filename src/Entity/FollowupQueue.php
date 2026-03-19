<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'followup_queue')]
class FollowupQueue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $emailHash;

    #[ORM\Column(type: 'text')]
    private string $encryptedEmail;

    #[ORM\Column(type: 'string', length: 2)]
    private string $countryCode;

    #[ORM\Column(type: 'string', length: 10)]
    private string $locale;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $sendAt;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $sent = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    public function __construct(
        string $emailHash,
        string $encryptedEmail,
        string $countryCode,
        string $locale,
        \DateTimeImmutable $sendAt,
    ) {
        $this->emailHash = $emailHash;
        $this->encryptedEmail = $encryptedEmail;
        $this->countryCode = $countryCode;
        $this->locale = $locale;
        $this->sendAt = $sendAt;
    }

    public function getId(): int { return $this->id; }
    public function getEmailHash(): string { return $this->emailHash; }
    public function getEncryptedEmail(): string { return $this->encryptedEmail; }
    public function wipeEmail(): void { $this->encryptedEmail = ''; }
    public function getCountryCode(): string { return $this->countryCode; }
    public function getLocale(): string { return $this->locale; }
    public function getSendAt(): \DateTimeImmutable { return $this->sendAt; }
    public function isSent(): bool { return $this->sent; }

    public function markSent(): void
    {
        $this->sent = true;
        $this->sentAt = new \DateTimeImmutable();
    }

    public function getSentAt(): ?\DateTimeImmutable { return $this->sentAt; }
}
