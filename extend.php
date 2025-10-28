<?php namespace Aixueyuan\Search;

use Flarum\Discussion\Event\Saving;
use Flarum\Discussion\Search\DiscussionSearcher;
use Flarum\Extend;
use Aixueyuan\Search\Controller\ListSuggestWordsController;
use Aixueyuan\Search\Gambit\TitleGambit;
use Aixueyuan\Search\Listener\UpdateDiscussionTokens;

return [
    (new Extend\SimpleFlarumSearch(DiscussionSearcher::class))
        ->setFullTextGambit(TitleGambit::class),

    (new Extend\Event())
        ->listen(Saving::class, UpdateDiscussionTokens::class),

    (new Extend\Routes('api'))
        ->get('/search/suggestions', 'search.suggestions.index', ListSuggestWordsController::class),
];