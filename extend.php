<?php

use Flarum\Discussion\Event\Saving;
use Flarum\Discussion\Search\DiscussionSearcher;
use Flarum\Extend;
use Aixueyuan\Search\Console\SegmentDiscussionsCommand;
use Aixueyuan\Search\Controller\ListSuggestWordsController;
use Aixueyuan\Search\Gambit\SegmentedFulltextGambit;
use Aixueyuan\Search\Listener\UpdateDiscussionTokens;

return [
    (new Extend\Event())
        ->listen(Saving::class, UpdateDiscussionTokens::class),

    (new Extend\SimpleFlarumSearch(DiscussionSearcher::class))
        ->setFullTextGambit(SegmentedFulltextGambit::class),

    (new Extend\Routes('api'))
        ->get('/search/suggestions', 'search.suggestions.index', ListSuggestWordsController::class),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Console())
        ->command(SegmentDiscussionsCommand::class),
];