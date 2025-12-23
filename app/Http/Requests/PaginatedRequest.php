<?php

namespace App\Http\Requests;

abstract class PaginatedRequest extends ApiRequest
{
    /**
     * Common pagination validation rules.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
            'sortBy' => 'nullable|string',
            'sortDirection' => 'nullable|in:asc,desc',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function page(): int
    {
        return (int) $this->input('page', 1);
    }

    public function limit(): int
    {
        return (int) $this->input('limit', 10);
    }

    public function sortBy(string $default = 'created_at'): string
    {
        return (string) $this->input('sortBy', $default);
    }

    public function sortDirection(string $default = 'asc'): string
    {
        $dir = strtolower((string) $this->input('sortDirection', $default));

        return in_array($dir, ['asc', 'desc'], true) ? $dir : $default;
    }
}
