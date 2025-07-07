<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetAvailableRoomsRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'begin_date' => [
                'nullable',
                'required_with:end_date',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'end_date' => [
                'nullable',
                'required_with:begin_date',
                'date_format:Y-m-d',
                'after_or_equal:begin_date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'begin_date.required_with' => 'Если передана дата окончания периода, то должна быть передана дата начала',
            'begin_date.date_format' => 'Дата начала периода должна быть в формате YYYY-MM-DD',
            'begin_date.after_or_equal' => 'Дата начала периода не может быть в прошлом',
            'end_date.required_with' => 'Если передана дата начала периода, то должна быть передана дата окончания',
            'end_date.date_format' => 'Дата окончания периода должна быть в формате YYYY-MM-DD',
            'end_date.after_or_equal' => 'Дата окончания периода не может быть меньше даты начала периода',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Ошибки валидации',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
