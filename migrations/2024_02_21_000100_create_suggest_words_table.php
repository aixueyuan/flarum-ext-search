<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable('suggest_words', function (Blueprint $table) {
    $table->increments('id');
    $table->string('word', 191);
    $table->boolean('is_manual')->default(false);
    $table->unsignedInteger('weight')->default(1);
    $table->unsignedInteger('usage_count')->default(0);
    $table->timestamp('last_used_at')->nullable();
    $table->timestamps();

    $table->unique('word');
});