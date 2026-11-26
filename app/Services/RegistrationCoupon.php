<?php

namespace App\Services;

use App\Models\Inscription;
use App\Models\SpecialCode;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class RegistrationCoupon
{
    public function assignedToEmail(?string $email): Collection
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return collect();
        }

        return SpecialCode::query()
            ->with('categories')
            ->where('status', 'Activo')
            ->whereDate('expiration', '>=', Carbon::now('America/Lima')->toDateString())
            ->where(function ($query) use ($email) {
                $query->whereRaw('LOWER(allowed_email) = ?', [$email])
                    ->orWhereHas('allowedEmails', fn ($emails) => $emails->whereRaw('LOWER(email) = ?', [$email]));
            })
            ->orderBy('code')
            ->get();
    }

    public static function normalizeAllowedEmails(?string $input): array
    {
        $input = trim((string) $input);
        if (strlen($input) > 50000) {
            throw ValidationException::withMessages(['allowed_emails' => 'The email list is too long.']);
        }
        $emails = array_values(array_unique(array_map('strtolower', preg_split('/[\s,;]+/u', $input, -1, PREG_SPLIT_NO_EMPTY))));
        if (count($emails) > 1000) {
            throw ValidationException::withMessages(['allowed_emails' => 'A Special Code can list up to 1,000 email addresses.']);
        }
        foreach ($emails as $email) {
            if (strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages(['allowed_emails' => "Invalid email address: {$email}"]);
            }
        }
        return $emails;
    }

    public function check(?string $input, int $categoryId, string $email, ?int $exceptInscriptionId = null, bool $lock = false): SpecialCode
    {
        $code = strtoupper(trim((string) $input));
        $email = strtolower(trim($email));
        $query = SpecialCode::whereRaw('UPPER(code) = ?', [$code]);
        $coupon = ($lock ? $query->lockForUpdate() : $query)->first();

        if (!$coupon || $coupon->status !== 'Activo') {
            $this->fail('Invalid or inactive Special Code.');
        }
        if (!$coupon->expiration || Carbon::parse($coupon->expiration, 'America/Lima')->endOfDay()->lt(Carbon::now('America/Lima'))) {
            $this->fail('This Special Code has expired.');
        }
        $category = $coupon->categories()->whereKey($categoryId)->first();
        if (!$category) {
            $this->fail('This Special Code does not apply to the selected category.');
        }
        if ($category->name === 'Special Code' && ((float) $coupon->amount !== 0.0 || $coupon->payment_required !== 'No')) {
            $this->fail('The Special Code category requires a zero amount and no payment.');
        }
        $allowedEmails = $coupon->allowedEmails()->pluck('email')->map(fn ($value) => strtolower(trim($value)))->all();
        if ($allowedEmails && !in_array($email, $allowedEmails, true)) {
            $this->fail('This Special Code is reserved for another email address.');
        }
        if (!$allowedEmails && (!$coupon->allowed_email || strtolower(trim($coupon->allowed_email)) !== $email)) {
            $this->fail('This Special Code is reserved for another email address.');
        }

        $uses = Inscription::query()
            ->whereRaw('UPPER(special_code) = ?', [$code])
            ->whereNotIn('status', ['Draft', 'Refused', 'Rechazado'])
            ->when($exceptInscriptionId, fn ($q) => $q->where('id', '!=', $exceptInscriptionId));
        $limit = $coupon->usage_rule === 'single_use' ? 1 : (int) $coupon->quantity;
        if ($limit < 1 || (clone $uses)->count() >= $limit) {
            $this->fail('This Special Code has reached its usage limit.');
        }
        if ($coupon->usage_rule === 'per_email' && (clone $uses)
            ->where(function ($q) use ($email) {
                $q->whereRaw('LOWER(special_code_email) = ?', [$email])
                    ->orWhere(function ($legacy) use ($email) {
                        $legacy->whereNull('special_code_email')->whereHas('user', fn ($user) => $user->whereRaw('LOWER(email) = ?', [$email]));
                    });
            })->exists()) {
            $this->fail('This email address has already used this Special Code.');
        }

        return $coupon;
    }

    private function fail(string $message): void
    {
        throw ValidationException::withMessages(['specialcode' => $message]);
    }
}
