<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
class StoreTaskRequest extends FormRequest
{
 
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => [
                'required',
                Rule::enum(Priority::class),
            ],
            'status' => [
                'required',
                Rule::enum(Status::class),
            ],
            'due_date' => 'nullable|date',
        ];
    }
}
