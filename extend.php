<?php namespace Aixueyuan\Search;

use Flarum\Discussion\Event\Saving;
use Flarum\Discussion\Search\DiscussionSearcher;
use Flarum\Discussion\Search\Fulltext\DriverInterface;
use Flarum\Extend;
use Illuminate\Contracts\Container\Container;
use Aixueyuan\Search\Controller\ListSuggestWordsController;
use Aixueyuan\Search\Driver\MySqlDiscussionTitleDriver;
use Aixueyuan\Search\Gambit\TitleGambit;
use Aixueyuan\Search\Listener\UpdateDiscussionTokens;

return [
    (new Extend\SimpleFlarumSearch(DiscussionSearcher::class))
        ->setFullTextGambit(TitleGambit::class),

    (new Extend\ServiceProvider())
        ->register(function (Container $container) {
            $container->bind(DriverInterface::class, MySqlDiscussionTitleDriver::class);
        }),

    (new Extend\Event())
        ->listen(Saving::class, UpdateDiscussionTokens::class),

    (new Extend\Routes('api'))
        ->get('/search/suggestions', 'search.suggestions.index', ListSuggestWordsController::class),
];
