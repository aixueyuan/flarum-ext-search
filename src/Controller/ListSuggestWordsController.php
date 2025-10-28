<?php

namespace Aixueyuan\Search\Controller;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Aixueyuan\Search\Model\SuggestWord;
use Aixueyuan\Search\Serializer\SuggestWordSerializer;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListSuggestWordsController extends AbstractListController
{
    public $serializer = SuggestWordSerializer::class;

    protected $limit = 10;

    protected $maxLimit = 20;

    protected function data(ServerRequestInterface $request, Document $document): Collection
    {
        RequestUtil::getActor($request); // 允许访客访问，无需额外权限断言

        $params = $request->getQueryParams();
        $term = trim((string) ($params['q'] ?? ''));
        $limit = (int) ($params['limit'] ?? $this->limit);
        $limit = max(1, min($this->maxLimit, $limit));

        $query = SuggestWord::query();

        if ($term !== '') {
            $query->where('word', 'like', '%' . $term . '%');
        }

        $results = $query
            ->orderByDesc('weight')
            ->orderBy('word')
            ->limit(60)
            ->get();

        if ($term === '') {
            return $results->take($limit)->values();
        }

        $sorted = $results->sort(function (SuggestWord $a, SuggestWord $b) use ($term) {
            $rankA = $this->matchRank($a->word, $term);
            $rankB = $this->matchRank($b->word, $term);

            if ($rankA === $rankB) {
                if ($a->weight === $b->weight) {
                    return strcmp($a->word, $b->word);
                }

                return $b->weight <=> $a->weight;
            }

            return $rankA <=> $rankB;
        })->values();

        if ($sorted->isEmpty()) {
            return collect([new SuggestWord([
                'word' => $term,
                'weight' => 0,
                'is_manual' => false,
            ])]);
        }

        return $sorted->take($limit);
    }

    protected function matchRank(string $word, string $term): int
    {
        $wordLower = mb_strtolower($word, 'UTF-8');
        $termLower = mb_strtolower($term, 'UTF-8');

        if ($wordLower === $termLower) {
            return 0;
        }

        if (mb_strpos($wordLower, $termLower) === 0) {
            return 1;
        }

        return 2;
    }
}