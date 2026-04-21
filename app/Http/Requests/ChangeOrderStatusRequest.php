<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeOrderStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reason' => 'nullable|string|max:500'
        ];
    }
}
