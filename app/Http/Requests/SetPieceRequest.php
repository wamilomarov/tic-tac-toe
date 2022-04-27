<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetPieceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'x' => ['required', 'integer', 'gte:0', 'lte:2'],
            'y' => ['required', 'integer', 'gte:0', 'lte:2'],
        ];
    }
}
