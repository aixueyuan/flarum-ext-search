<?php

namespace Aixueyuan\Search;

use Flarum\Extend;

return [
    (new Extend\ServiceProvider())
        ->register(Provider\SearchServiceProvider::class),
];
