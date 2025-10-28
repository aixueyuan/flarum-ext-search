<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasColumn('discussions', 'segmented_title')) {
            $schema->table('discussions', function (Blueprint $table) {
                $table->text('segmented_title')->nullable();
            });
        }

        try {
            DB::statement('ALTER TABLE discussions ADD FULLTEXT INDEX segmented_title_index (segmented_title)');
        } catch (\Exception $e) {
        }
    },

    'down' => function (Builder $schema) {
        try {
            DB::statement('ALTER TABLE discussions DROP INDEX segmented_title_index');
        } catch (\Exception $e) {
        }

        if ($schema->hasColumn('discussions', 'segmented_title')) {
            $schema->table('discussions', function (Blueprint $table) {
                $table->dropColumn('segmented_title');
            });
        }
    },
];
