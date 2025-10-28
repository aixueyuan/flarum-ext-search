<?php

namespace Aixueyuan\Search\Model;

use Flarum\Database\AbstractModel;

class SuggestWord extends AbstractModel
{
    protected $table = 'suggest_words';

    protected $fillable = [
        'word',
        'is_manual',
        'weight',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'is_manual' => 'boolean',
        'weight' => 'integer',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
    ];
}