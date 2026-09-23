<?php

namespace App\Http\Requests;

use App\Models\ReviewerCandidate;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AssignAbstractReviewersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('Administrador');
    }

    public function rules(): array
    {
        return [
            'reviewer_ids' => ['nullable', 'array', 'max:3'],
            'reviewer_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $ids = collect($this->input('reviewer_ids', []))
                ->filter(function ($id) {
                    return filter_var($id, FILTER_VALIDATE_INT) !== false;
                })
                ->map(function ($id) {
                    return (int) $id;
                })
                ->unique()
                ->values();

            if ($ids->isEmpty()) {
                return;
            }

            $eligibleCount = User::query()
                ->whereIn('id', $ids)
                ->whereIn('email', ReviewerCandidate::query()->select('email'))
                ->count();

            if ($eligibleCount !== $ids->count()) {
                $validator->errors()->add(
                    'reviewer_ids',
                    'Every selected reviewer must appear in Reviewer Candidates with the same email address.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'reviewer_ids.max' => 'An abstract can have a maximum of 3 reviewers.',
            'reviewer_ids.*.distinct' => 'The same reviewer cannot be assigned more than once.',
        ];
    }
}
