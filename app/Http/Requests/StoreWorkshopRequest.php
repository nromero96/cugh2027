<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreWorkshopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'submission_token' => 'required|string|max:2048',
            'lead_name' => 'required|string|max:255',
            'lead_institution' => 'required|string|max:255',
            'lead_title' => 'required|string|max:255',
            'lead_email' => 'required|email|max:255',
            'lead_phone' => 'required|string|max:255',
            'lead_cell' => 'required|string|max:255',
            'workshop_title' => 'required|string|max:255',
            'workshop_desc' => ['bail', 'required', 'string', 'max:20000', function ($attribute, $value, $fail) {
                if (count(preg_split('/\s+/u', trim($value), -1, PREG_SPLIT_NO_EMPTY)) > 200) {
                    $fail('The workshop description must not exceed 200 words.');
                }
            }],
            'workshop_objectives' => 'required|string|max:1000',
            'workshop_speakers' => 'nullable|string|max:1000',
            'time_slot' => ['required', \Illuminate\Validation\Rule::in([
                'Morning, 9am-12pm', 'Afternoon, 1pm-4pm', 'Full Day, 9am-4pm',
            ])],
            'day_length' => 'required|in:Half Day,Full Day',
            'room_setup' => 'required|in:theater,rounds',
            'attendees' => 'required|integer|min:1|max:2147483647',
            'notes' => 'nullable|string|max:5000',
            'payment_lead_same' => 'required|in:Yes,No',
            'terms' => 'accepted',
            'signature' => ['bail', 'required', 'string', 'max:1400000', function ($attribute, $value, $fail) {
                if (self::decodeSignature($value) === null) {
                    $fail('Please provide a valid PNG signature (maximum 1 MB).');
                }
            }],
            'place_date' => 'required|date_format:Y-m-d',
        ];

        foreach (['name', 'institution', 'title', 'email', 'phone', 'cell'] as $field) {
            $rules['payment_'.$field] = 'exclude_if:payment_lead_same,Yes|required_if:payment_lead_same,No|nullable|'
                .($field === 'email' ? 'email' : 'string').'|max:255';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (in_array($this->time_slot, ['Morning, 9am-12pm', 'Afternoon, 1pm-4pm', 'Full Day, 9am-4pm'], true)
                && in_array($this->day_length, ['Half Day', 'Full Day'], true)
                && (($this->time_slot === 'Full Day, 9am-4pm') !== ($this->day_length === 'Full Day'))) {
                $validator->errors()->add('day_length', 'The selected duration must match the preferred time slot.');
            }
        });
    }

    public static function decodeSignature(string $value): ?string
    {
        $prefix = 'data:image/png;base64,';
        if (strlen($value) > 1400000 || strpos($value, $prefix) !== 0) {
            return null;
        }

        $image = base64_decode(substr($value, strlen($prefix)), true);
        if ($image === false || $image === '' || strlen($image) > 1048576) {
            return null;
        }

        $size = @getimagesizefromstring($image);
        if (!$size || $size[2] !== IMAGETYPE_PNG || $size[0] > 4096 || $size[1] > 4096) {
            return null;
        }

        return $image;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(redirect()->back()
            ->withErrors($validator)
            ->withInput($this->except('signature', '_token')));
    }
}
