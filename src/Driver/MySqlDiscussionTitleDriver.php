<?php

namespace Aixueyuan\Search\Driver;

use Flarum\Discussion\Search\Fulltext\MySqlFulltextDriver;

class MySqlDiscussionTitleDriver extends MySqlFulltextDriver
{
    /**
     * 让 MATCH 同时作用在 segmented_title 与 title 上
     */
    protected array $columns = ['segmented_title', 'title'];

    /**
     * 去掉额外空白，避免 “武侠 is:solved” 这类串进 MATCH
     */
    protected function prepare(string $string): string
    {
        return trim(preg_replace('/\s+/u', ' ', $string));
    }
}