<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->vehicle?->id ?? null;

        return [
            'external_id'  => 'sometimes|integer|unique:vehicles,external_id,' . $vehicleId,
            'type'         => 'nullable|string|max:50',
            'brand'        => 'nullable|string|max:100',
            'model'        => 'nullable|string|max:100',
            'version'      => 'nullable|string|max:150',
            'year_model'   => 'nullable|string|size:4',
            'year_build'   => 'nullable|string|size:4',
            'optionals'    => 'nullable|array',
            'doors'        => 'nullable|integer',
            'board'        => 'nullable|string|max:20',
            'chassi'       => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
            'km'           => 'nullable|integer',
            'description'  => 'nullable|string',
            'sold'         => 'nullable|boolean',
            'category'     => 'nullable|string|max:50',
            'url_car'      => 'nullable|string|max:150',
            'old_price'    => 'nullable|numeric|min:0',
            'price'        => 'sometimes|numeric|min:0',
            'color'        => 'nullable|string|max:50',
            'fuel'         => 'nullable|string|max:50',
            'photos'       => 'nullable|array',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('optionals') && is_string($this->optionals)) {
            $this->merge([
                'optionals' => json_decode($this->optionals, true),
            ]);
        }

        if ($this->has('photos') && is_string($this->photos)) {
            $this->merge([
                'photos' => json_decode($this->photos, true),
            ]);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Os dados fornecidos são inválidos.',
            'errors'  => $validator->errors(),
            'data'    => null,
        ], 422));
    }
}
