<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceIndexRequest extends FormRequest
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
            'date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:present,late,absent,on_leave'],
            'employee_id' => ['nullable', 'integer'],
        ];
    }
}
