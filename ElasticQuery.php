<?php

namespace App\Service\ElasticSearch;

class ElasticQuery
{
    /** @var array */
    private $body = [];
    private $filters = [];

    /**
     * Types: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
     * - must
     * - must_not
     * - should
     * - filter
     */
    public function getQuery(string $type = 'must'): array
    {
        $query = [
            'bool' => []
        ];

        if ($this->body) {
            $query['bool'][$type] = $this->body;
        }

        if ($this->filters) {
            $query['bool']['filter'] = $this->filters;
        }

        return [
            'query' => $query
        ];
    }

    //------------------------------------------------------------------------------------------------------------------

    public function limit(int $from, int $size): self
    {
        $this->body['from'] = $from;
        $this->body['size'] = $size;
        return $this;
    }

    public function sort(array $sorting)
    {
        foreach ($sorting as $sort) {
            [$field, $order] = $sort;
            $this->body['sort'][] = [
                $field => $order
            ];
        }

        return $this;
    }

    public function minScore(float $score): self
    {
        $this->body['min_score'] = $score;
        return $this;
    }

    //------------------------------------------------------------------------------------------------------------------

    public function all(): self
    {
        $this->body['match_all'] = [
            "boost" => 1
        ];
        return $this;
    }

    public function queryTerm(string $field, string $value): self
    {
        $this->body[]['term'] = [
            $field => strtolower($value)
        ];
        return $this;
    }

    public function queryWildcard(string $field, string $value): self
    {
        $this->body[]['wildcard'] = [
            $field => sprintf('*%s*', strtolower($value))
        ];
        return $this;
    }

    public function queryPrefix(string $field, string $value): self
    {
        $this->body[]['prefix'] = [
            $field => $value
        ];
        return $this;
    }

    public function queryMatch(string $field, string $value): self
    {
        $this->body[]['match'] = [
            $field => strtolower($value)
        ];
        return $this;
    }

    public function queryMatchPhrase(string $field, string $value): self
    {
        $this->body[]['match_phrase'] = [
            $field => strtolower($value)
        ];
        return $this;
    }

    public function queryMatchPhrasePrefix(string $field, string $value): self
    {
        $this->body[]['match_phrase_prefix'] = [
            $field => strtolower($value)
        ];
        return $this;
    }

    public function queryMultiMatch(array $fields, string $value): self
    {
        $this->body[]['multi_match'] = [
            'query' => strtolower($value),
            'fields' => $fields
        ];
        return $this;
    }

    // todo - does not work
    // https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html
    public function queryString(string $field, string $query): self
    {
        $this->body[]['query_string'][] = [
            'default_field' => $field,
            'query' => $query
        ];
        return $this;
    }

    public function filterTerm(string $field, string $value): self
    {
        $this->filters[] = [
            'term' => [
                $field => strtolower($value),
            ]
        ];
        return $this;
    }

    public function filterRange(string $field, string $value, string $condition): self
    {
        $this->filters[] = [
            'range' => [
                $field => [ $condition => strtolower($value) ]
            ]
        ];
        return $this;
    }
}
