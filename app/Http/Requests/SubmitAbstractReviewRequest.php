<?php

namespace App\Http\Requests;

use App\Models\AbstractPost;
use Illuminate\Foundation\Http\FormRequest;

class SubmitAbstractReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $abstractPost = $this->route('abstractPost');

        return $user
            && $abstractPost instanceof AbstractPost
            && $abstractPost->reviewers()->where('users.id', $user->id)->exists();
    }

    public function rules(): array
    {
        return [
            'score_1' => ['required', 'integer', 'between:0,10'],
            'score_2' => ['required', 'integer', 'between:0,10'],
            'score_3' => ['required', 'integer', 'between:0,10'],
            'score_4' => ['required', 'integer', 'between:0,10'],
            'score_5' => ['required', 'integer', 'between:0,10'],
            'reviewer_note' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'score_*.required' => 'All five scores are required.',
            'score_*.integer' => 'Each score must be a whole number.',
            'score_*.between' => 'Each score must be between 0 and 10.',
            'reviewer_note.max' => 'The review note may not be longer than 5,000 characters.',
        ];
    }
}
