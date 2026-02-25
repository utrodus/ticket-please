<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules =  [
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
        ];

        if ($this->routeIs('ticket.store')) {
            $rules['data.relationships.author.data.id'] = 'required|integer|exists:users,id';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'data.attributes.status' => 'The data.attributes.status field must be one of the following values: A (Active), C (Closed), H (On Hold), X (Cancelled).',
            'data.relationships.author.data.id.exists' => 'The specified author does not exist. Please provide a valid user ID.',
        ];
    }
}
