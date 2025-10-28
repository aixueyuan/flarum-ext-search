<?php

namespace Aixueyuan\Search\Listener;

use Flarum\Discussion\Event\Saving;
use Aixueyuan\Search\Model\SuggestWord;
use Aixueyuan\Search\Tokenizer\JiebaTokenizer;
use Illuminate\Support\Arr;

class UpdateDiscussionTokens
{
    public function __construct(protected JiebaTokenizer $tokenizer)
    {
    }

    public function handle(Saving $event): void
    {
        $discussion = $event->discussion;
        $attributes = Arr::get($event->data, 'attributes', []);

        $incomingTitle = Arr::get($attributes, 'title');
        $title = $incomingTitle !== null ? (string) $incomingTitle : (string) $discussion->title;

        if (trim($title) === '') {
            return;
        }

        $titleChanged = $incomingTitle !== null
            ? true
            : (!$discussion->exists || $discussion->title !== $discussion->getOriginal('title'));

        $existingSegmented = (string) ($discussion->getOriginal('segmented_title') ?? '');
        $shouldRebuild = $titleChanged || $existingSegmented === '';

        if (! $shouldRebuild) {
            return;
        }

        $tokens = $this->prepareTokens($title);
        $segmented = $this->tokenizer->joinTokens($tokens);

        $discussion->setAttribute('segmented_title', $segmented);

        $oldTokens = $this->tokenizer->splitString($existingSegmented);
        $this->syncSuggestWords($oldTokens, $tokens);
    }

    protected function prepareTokens(string $title): array
    {
        $tokens = $this->tokenizer->segment($title);

        $unique = [];
        foreach ($tokens as $token) {
            $key = mb_strtolower($token, 'UTF-8');
            $unique[$key] = $token;
        }

        return array_values($unique);
    }

    protected function syncSuggestWords(array $oldTokens, array $newTokens): void
    {
        $oldMap = $this->normalizeTokens($oldTokens);
        $newMap = $this->normalizeTokens($newTokens);

        $toAdd = array_diff_key($newMap, $oldMap);
        $toRemove = array_diff_key($oldMap, $newMap);

        foreach ($toAdd as $token) {
            $this->incrementSuggestion($token);
        }

        foreach ($toRemove as $token) {
            $this->decrementSuggestion($token);
        }
    }

    protected function normalizeTokens(array $tokens): array
    {
        $normalized = [];

        foreach ($tokens as $token) {
            $token = trim($token);

            if ($token === '') {
                continue;
            }

            $normalized[mb_strtolower($token, 'UTF-8')] = $token;
        }

        return $normalized;
    }

    protected function incrementSuggestion(string $token): void
    {
        /** @var SuggestWord|null $suggest */
        $suggest = SuggestWord::query()->where('word', $token)->first();

        if ($suggest === null) {
            SuggestWord::query()->create([
                'word' => $token,
                'is_manual' => false,
                'weight' => 1,
            ]);

            return;
        }

        $suggest->weight = max(1, (int) $suggest->weight + 1);
        $suggest->save();
    }

    protected function decrementSuggestion(string $token): void
    {
        /** @var SuggestWord|null $suggest */
        $suggest = SuggestWord::query()->where('word', $token)->first();

        if ($suggest === null || $suggest->is_manual) {
            return;
        }

        $suggest->weight = max(0, (int) $suggest->weight - 1);

        if ($suggest->weight === 0) {
            $suggest->delete();
            return;
        }

        $suggest->save();
    }
}