<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class BookRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Имитируем получение токена - для этого в штатном режиме используется отдельный метод API
        $token = $this->createUserToken($this->input('user_id'), ['room:book']);

        // Имитируем использование токена - в штатном режиме получаем его из заголовка, авторизацию обрабатывает middleware
        $this->loginUsingToken($token);

        // Непосредственно проверка прав пользователя (+ возможность управлять только своими бронированиями)
        return $this->user()
            && $this->user()->id === (int)($this->input('user_id'))
            && $this->user()->tokenCan('room:book');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'begin_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'end_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:begin_date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Нужно указать ID пользователя',
            'user_id.exists' => 'Указанный пользователь не существует',
            'begin_date.required' => 'Нужно указать дату начала периода',
            'begin_date.date_format' => 'Формат даты начала периода: YYYY-MM-DD',
            'begin_date.after_or_equal' => 'Начало бронирования не может быть в прошлом',
            'end_date.required' => 'Нужно указать дату окончания периода',
            'end_date.date_format' => 'Формат даты окончания периода: YYYY-MM-DD',
            'end_date.after_or_equal' => 'Окончания бронирования не может быть раньше начала бронирования',
        ];
    }

    private function createUserToken(mixed $userId, array $abilities): ?string
    {
        if (!User::where('id', $userId)->exists()) {
            return null;
        }

        Auth::loginUsingId($userId);
        $token = $this->user()->createToken('user-token', $abilities)->plainTextToken;
        Auth::logout();

        return $token;
    }

    private function loginUsingToken(string $token): void
    {
        $accessToken = PersonalAccessToken::findToken($token);
        if ($accessToken) {
            auth()->login($accessToken->tokenable);
            $this->user()->withAccessToken($accessToken);
        }
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Не авторизован',
                'errors' => [
                    'auth' => ['Бронировать номера могут только авторизованные пользователи'],
                ],
            ], 403)
        );
    }

    /**
     * Handle a failed validation attempt.
     */
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
