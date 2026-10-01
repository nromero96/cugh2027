<?php

namespace App\Http\Requests;

use App\Models\ReviewerCandidate;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AssignPanelReviewersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('Administrador');
    }

    public function rules(): array
    {
        return [
            'reviewer_ids' => ['nullable', 'array', 'max:2'],
            'reviewer_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $ids = collect($this->input('reviewer_ids', []))->filter(function ($id) {
                return filter_var($id, FILTER_VALIDATE_INT) !== false;
            })->map(function ($id) {
                return (int) $id;
            })->unique();

            if ($ids->isNotEmpty() && User::whereIn('id', $ids)
                ->whereIn('email', ReviewerCandidate::select('email'))->count() !== $ids->count()) {
                $validator->errors()->add('reviewer_ids', 'Every selected reviewer must appear in Reviewer Directory with the same email address.');
            }
        });
    }

    public function messages(): array
    {
        return ['reviewer_ids.max' => 'A panel can have a maximum of 2 reviewers.'];
    }
}
