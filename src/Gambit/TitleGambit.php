<?php

namespace Aixueyuan\Search\Gambit;

use Aixueyuan\Search\Driver\MySqlDiscussionTitleDriver;
use Flarum\Search\GambitInterface;
use Flarum\Search\SearchState;

class TitleGambit implements GambitInterface
{
    public function __construct(protected MySqlDiscussionTitleDriver $driver)
    {
    }

    public function apply(SearchState $search, $bit)
    {
        $bit = trim((string) $bit);

        if ($bit === '') {
            return;
        }

        $result = $this->driver->match($bit);
        $orderedIds = $result['orderedIds'];
        $relevantPosts = $result['relevantPosts'];

        if ($orderedIds === []) {
            // 阻止 Flarum 回退到“最新帖子列表”
            $search->getQuery()->whereRaw('1 = 0');
            return;
        }

        $search->getQuery()->whereIn('discussions.id', $orderedIds);

        $search->setRelevantPosts($relevantPosts);

        $search->setDefaultSort(function ($query) use ($orderedIds) {
            $ids = implode(',', array_map('intval', $orderedIds));
            $query->orderByRaw("FIELD(discussions.id, {$ids})");
        });
    }
}