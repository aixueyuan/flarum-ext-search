<?php

namespace Aixueyuan\Search\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Aixueyuan\Search\Model\SuggestWord;

class SuggestWordSerializer extends AbstractSerializer
{
    protected $type = 'search-suggestions';

    /**
     * @param SuggestWord $suggestWord
     */
    protected function getDefaultAttributes($suggestWord): array
    {
        return [
            'word' => $suggestWord->word,
            'weight' => (int) $suggestWord->weight,
            'isManual' => (bool) $suggestWord->is_manual,
            'usageCount' => (int) $suggestWord->usage_count,
            'lastUsedAt' => $suggestWord->last_used_at
                ? $suggestWord->last_used_at->toAtomString()
                : null,
        ];
    }
}