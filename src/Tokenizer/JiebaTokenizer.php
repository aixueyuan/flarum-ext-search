<?php

namespace Aixueyuan\Search\Tokenizer;

use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;

class JiebaTokenizer
{
    protected static bool $initialized = false;

    protected function initialize(): void
    {
        if (static::$initialized) {
            return;
        }

        Jieba::init([
            'mode' => 'default',
            'dict' => 'big',
        ]);

        Finalseg::init();

        static::$initialized = true;
    }

    public function segment(string $text): array
    {
        $this->initialize();

        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if ($text === '') {
            return [];
        }

        $tokens = Jieba::cutForSearch($text);

        $filtered = array_filter(array_map('trim', $tokens), function (string $token) {
            if ($token === '') {
                return false;
            }

            return (bool) preg_match('/[\p{L}\p{N}]/u', $token);
        });

        return array_values($filtered);
    }

    public function joinTokens(array $tokens): string
    {
        $trimmed = array_filter(array_map('trim', $tokens), fn ($token) => $token !== '');

        return implode(' ', $trimmed);
    }

    public function splitString(string $segmented): array
    {
        $segmented = trim(preg_replace('/\s+/u', ' ', $segmented));

        if ($segmented === '') {
            return [];
        }

        return array_values(array_filter(explode(' ', $segmented), fn ($token) => $token !== ''));
    }
}