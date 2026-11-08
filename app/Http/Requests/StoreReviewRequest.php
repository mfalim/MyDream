<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required' => 'Pilih acara yang ingin diulas.',
            'event_id.exists' => 'Acara tidak ditemukan.',
            'rating.required' => 'Pilih rating bintang 1 sampai 5.',
            'rating.between' => 'Rating harus antara 1 sampai 5.',
            'comment.max' => 'Komentar maksimal 1.000 karakter.',
        ];
    }
}
