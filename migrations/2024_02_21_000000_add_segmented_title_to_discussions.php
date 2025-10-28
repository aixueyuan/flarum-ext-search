<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $this->schema()->table('discussions', function (Blueprint $table) {
            $table->text('segmented_title')->nullable();
        });

        DB::statement('ALTER TABLE discussions ADD FULLTEXT INDEX segmented_title_index (segmented_title);');
    }

    public function down()
    {
        $this->schema()->table('discussions', function (Blueprint $table) {
            $table->dropColumn('segmented_title');
        });

        DB::statement('ALTER TABLE discussions DROP INDEX segmented_title_index;');
    }
};
