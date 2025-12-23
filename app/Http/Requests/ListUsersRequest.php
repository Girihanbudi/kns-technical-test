<?php

namespace App\Http\Requests;

class ListUsersRequest extends PaginatedRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'sortBy' => 'nullable|in:name,email,created_at',
            'active' => 'nullable|boolean',
        ]);
    }

    /**
     * Normalize boolean-ish query strings for the active filter.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('active')) {
            $value = $this->input('active');

            if (is_string($value)) {
                $valueLower = strtolower($value);
                $normalized = in_array($valueLower, ['1', 'true', 'yes'], true) ? 1 :
                    (in_array($valueLower, ['0', 'false', 'no'], true) ? 0 : $value);

                $this->merge(['active' => $normalized]);
            }
        }
    }
}
