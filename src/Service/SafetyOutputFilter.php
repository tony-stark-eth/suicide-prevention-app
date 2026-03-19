<?php

declare(strict_types=1);

namespace App\Service;

final class SafetyOutputFilter
{
    private const BLOCKED_PATTERNS = [
        '/\b(overdos\w*|hang(ing|ed)|jump(ing|ed) from|firearms?|lethal dose|pills to die|med(ication|s) to (kill|end))\b/i',
        '/\d+\s*(mg|ml|tablets?|pills?|capsules?)\b/i',
        '/RULES\s*[—-]\s*follow/i',
        '/system prompt/i',
        '/these instructions/i',
        '/as an AI/i',
        '/how to (die|kill yourself|end your life|commit suicide)/i',
        '/\b(painless|easy|quick)\s+(way|method|means)\b/i',
    ];

    public function __construct(
        private readonly string $fallbackLetterDir,
    ) {}

    public function screen(string $text, string $locale = "de"): string
    {
        if ($text === '') {
            return $this->fallback($locale);
        }
        foreach (self::BLOCKED_PATTERNS as $pattern) {
            if (preg_match($pattern, $text)) {
                return $this->fallback($locale);
            }
        }
        return $text;
    }

    private function fallback(string $locale): string
    {
        $path = $this->fallbackLetterDir . '/fallback_letter.' . $locale . '.txt';
        if (!file_exists($path)) {
            $path = $this->fallbackLetterDir . '/fallback_letter.en.txt';
        }
        if (!file_exists($path)) {
            return "Dear friend,\n\nYou matter. We are glad you are here.";
        }
        return file_get_contents($path);
    }
}
