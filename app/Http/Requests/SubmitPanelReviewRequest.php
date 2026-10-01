<?php

namespace App\Http\Requests;

use App\Models\Panel;
use Illuminate\Foundation\Http\FormRequest;

class SubmitPanelReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $panel = $this->route('panel');

        return $this->user() && $panel instanceof Panel
            && $panel->reviewers()->where('users.id', $this->user()->id)->exists();
    }

    public function rules(): array
    {
        $rules = ['reviewer_note' => ['nullable', 'string', 'max:5000']];
        foreach (range(1, 8) as $number) {
            $rules['score_'.$number] = ['required', 'integer', 'between:1,10'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'score_*.required' => 'All eight scores are required.',
            'score_*.between' => 'Each score must be between 1 and 10.',
            'score_*.integer' => 'Each score must be a whole number.',
        ];
    }
}
