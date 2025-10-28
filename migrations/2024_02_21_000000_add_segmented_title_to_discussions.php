<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::addColumns('discussions', [
    'segmented_title' => ['text', 'nullable' => true],
]);