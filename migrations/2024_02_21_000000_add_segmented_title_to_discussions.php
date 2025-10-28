<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 添加 'segmented_title' 字段
        $this->schema()->table('discussions', function (Blueprint $table) {
            $table->text('segmented_title')->nullable();
        });

        // 使用 DB 执行 SQL 语句添加 FULLTEXT 索引
        DB::statement('ALTER TABLE discussions ADD FULLTEXT INDEX segmented_title_index (segmented_title);');
    }

    public function down()
    {
        // 删除 'segmented_title' 字段
        $this->schema()->table('discussions', function (Blueprint $table) {
            $table->dropColumn('segmented_title');
        });

        // 删除 FULLTEXT 索引
        DB::statement('ALTER TABLE discussions DROP INDEX segmented_title_index;');
    }
};
