<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

abstract class MobileApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mobile_id' => $this->header('X-Mobile-Id'),
        ]);
    }

    /**
     * @return array<string, list<string>>
     */
    protected function mobileIdRules(): array
    {
        return [
            'mobile_id' => ['required', 'uuid', 'exists:mobiles,id'],
        ];
    }
}
