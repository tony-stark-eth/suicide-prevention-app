<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\FollowupQueue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class FollowupService
{
    private const CIPHER = 'AES-256-CBC';
    private const SCHEDULE_OFFSETS = ['+24 hours', '+7 days', '+30 days'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
        private readonly string $appSecret,
        private readonly string $fromEmail,
        private readonly string $fromName,
        private readonly string $appUrl,
    ) {}

    public function schedule(string $email, string $countryCode, string $locale): void
    {
        $emailHash = hash('sha256', $email . $this->appSecret);

        // Remove any existing unsent rows for this hash (re-subscribe = reset)
        $existing = $this->em->getRepository(FollowupQueue::class)->findBy([
            'emailHash' => $emailHash,
            'sent' => false,
        ]);
        foreach ($existing as $row) {
            $this->em->remove($row);
        }

        $encryptedEmail = $this->encrypt($email);
        $now = new \DateTimeImmutable();

        foreach (self::SCHEDULE_OFFSETS as $offset) {
            $row = new FollowupQueue(
                emailHash: $emailHash,
                encryptedEmail: $encryptedEmail,
                countryCode: $countryCode,
                locale: $locale,
                sendAt: $now->modify($offset),
            );
            $this->em->persist($row);
        }

        $this->em->flush();
    }

    public function processQueue(): int
    {
        $now = new \DateTimeImmutable();
        $due = $this->em->createQuery(
            'SELECT q FROM App\Entity\FollowupQueue q WHERE q.sent = false AND q.sendAt <= :now'
        )->setParameter('now', $now)->getResult();

        $sent = 0;
        foreach ($due as $row) {
            /** @var FollowupQueue $row */
            try {
                $email = $this->decrypt($row->getEncryptedEmail());
                $this->sendCheckin($email, $row->getCountryCode(), $row->getLocale(), $row->getEmailHash());
                $row->markSent();
                $row->wipeEmail();
                $sent++;
            } catch (\Exception) {
                // Log silently — do not expose email content
            }
        }

        // Delete rows sent more than 24h ago
        $this->em->createQuery(
            'DELETE FROM App\Entity\FollowupQueue q WHERE q.sent = true AND q.sentAt < :cutoff'
        )->setParameter('cutoff', $now->modify('-24 hours'))->execute();

        $this->em->flush();
        return $sent;
    }

    public function cancelByToken(string $token): void
    {
        $this->em->createQuery(
            'DELETE FROM App\Entity\FollowupQueue q WHERE q.emailHash = :hash AND q.sent = false'
        )->setParameter('hash', $token)->execute();
    }

    private function encrypt(string $plaintext): string
    {
        $key = hash('sha256', $this->appSecret, true);
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($plaintext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }

    private function decrypt(string $ciphertext): string
    {
        $raw = base64_decode($ciphertext);
        $iv = substr($raw, 0, 16);
        $encrypted = substr($raw, 16);
        $key = hash('sha256', $this->appSecret, true);
        return openssl_decrypt($encrypted, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
    }

    private function sendCheckin(string $email, string $countryCode, string $locale, string $unsubToken): void
    {
        $unsubUrl = $this->appUrl . '/' . $locale . '/followup/stop/' . $unsubToken;
        $planUrl = $this->appUrl . '/' . $locale . '/plan';

        $message = (new Email())
            ->from($this->fromEmail)
            ->to($email)
            ->subject('Kurzes Hallo von uns')
            ->html(sprintf(
                '<p>Wir wollten kurz nachfragen, wie es dir geht.</p>'
                . '<p><a href="%s">Dein Sicherheitsplan ist noch da</a>, wenn du ihn brauchst.</p>'
                . '<p>Du findest Hilfsangebote unter: <a href="%s/resources">Hilfe finden</a></p>'
                . '<p><small>Keine weiteren Nachrichten: <a href="%s">Abmelden</a></small></p>',
                htmlspecialchars($planUrl),
                htmlspecialchars($this->appUrl . '/' . $locale),
                htmlspecialchars($unsubUrl),
            ));

        $this->mailer->send($message);
    }
}
