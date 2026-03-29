<?php

namespace App\Modules\Chat\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:1000', 'required_without:attachment'],
            'attachment' => [
                'nullable',
                'file',
                'max:10240',
                'required_without:message',
                'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip',
            ],
        ];
    }
}
