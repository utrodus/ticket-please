<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{

    public function mappedAttributes()
    {
        $attributeMap = [
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',

            'data.relationships.author.data.id' => 'user_id',
        ];

        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }
        return $attributesToUpdate;
    }

    public function messages()
    {
        return [
            'data.attributes.status' => 'The data.attributes.status field must be one of the following values: A (Active), C (Closed), H (On Hold), X (Cancelled).',
            'data.relationships.author.data.id.exists' => 'The specified author does not exist. Please provide a valid user ID.',
        ];
    }
}
