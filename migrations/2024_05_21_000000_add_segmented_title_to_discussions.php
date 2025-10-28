<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return [
    Migration::addColumns('discussions', [
        'segmented_title' => ['text', 'nullable' => true],
    ]),

    Migration::run(
        function (Builder $schema) {
            $schema->table('discussions', function (Blueprint $table) {
                $table->fullText('segmented_title', 'idx_discussions_segmented_title_fulltext');
            });
        },
        function (Builder $schema) {
            $schema->table('discussions', function (Blueprint $table) {
                $table->dropFullText('idx_discussions_segmented_title_fulltext');
            });
        }
    ),
];