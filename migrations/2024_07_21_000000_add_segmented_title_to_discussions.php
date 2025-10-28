<?php

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;

return [
    'up' => function (Builder $schema) {
        $schema->table('discussions', function (Blueprint $table) {
            $table->text('segmented_title')->nullable();
        });

        $schema->table('discussions', function (Blueprint $table) {
            $table->fullText('segmented_title', 'idx_discussions_segmented_title_fulltext');
        });
    },

    'down' => function (Builder $schema) {
        $schema->table('discussions', function (Blueprint $table) {
            $table->dropFullText('idx_discussions_segmented_title_fulltext');
            $table->dropColumn('segmented_title');
        });
    },
];