<?php

declare(strict_types=1);

namespace App\Modules\Notification\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NotificationIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'channel' => ['nullable', 'string', 'in:email,sms,push,webhook'],
            'status' => ['nullable', 'string', 'in:pending,delivered,failed'],
            'recipient' => ['nullable', 'string', 'max:100'],
        ];
    }
}
