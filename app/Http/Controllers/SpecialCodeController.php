<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpecialCode;
use App\Models\Inscription;
use App\Models\CategoryInscription;
use App\Services\RegistrationCoupon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


class SpecialCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->routeIs('specialcodes.validatespecialcode')) {
                return $next($request);
            }
            abort_unless(auth()->check() && auth()->user()->hasRole('Administrador'), 403);
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'category_name' => 'specialcodes',
            'page_name' => 'specialcodes',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];
        //$specialcodes = SpecialCode::orderBy('id', 'desc')->get();

        $specialcodes = SpecialCode::with(['categories', 'allowedEmails'])->select('special_codes.*')
            ->selectSub(function ($query) {
                $query->from('inscriptions')->selectRaw('COUNT(*)')
                    ->whereColumn('inscriptions.special_code', 'special_codes.code')
                    ->whereNotIn('inscriptions.status', ['Draft', 'Refused', 'Rechazado']);
            }, 'used_count')
            ->orderBy('special_codes.id', 'desc')
            ->get();



        return view('pages.specialcodes.index')->with($data)->with('specialcodes', $specialcodes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'category_name' => 'specialcodes',
            'page_name' => 'specialcodes_create',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];
        return view('pages.specialcodes.create')->with($data)->with('categories', CategoryInscription::where('status', 'active')->where('name', '!=', 'Special Code')->orderBy('order')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate
        $this->validate($request, [
            'code' => 'required|string|max:100|unique:special_codes,code',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable',
            'quantity' => 'required|integer|min:1',
            'expiration' => 'required|date',
            'payment_required' => 'required|in:Si,No',
            'status' => 'required|in:Activo,Inactivo',
            'usage_rule' => 'required|in:limited,single_use,per_email',
            'allowed_emails' => 'required|string|max:50000',
            'categories' => 'required|array|min:1',
            'categories.*' => ['integer', 'distinct', Rule::exists('category_inscriptions', 'id')->where(function ($query) {
                $query->where('status', 'active')->where('name', '!=', 'Special Code');
            })],
        ]);
        $allowedEmails = RegistrationCoupon::normalizeAllowedEmails($request->input('allowed_emails'));
        if (!$allowedEmails) {
            throw ValidationException::withMessages(['allowed_emails' => 'Enter at least one authorized email address.']);
        }
        if ($request->input('payment_required') === 'Si' && (float) $request->input('amount') <= 0) {
            throw ValidationException::withMessages(['amount' => 'Enter an amount greater than zero when payment is required.']);
        }

        //create
        DB::beginTransaction();
        try {
            $specialcode = new SpecialCode;
            $specialcode->code = strtoupper(trim($request->input('code')));
            $specialcode->amount = $request->input('amount');
            $specialcode->description = $request->input('description');
            $specialcode->quantity = $request->input('usage_rule') === 'single_use' ? 1 : $request->input('quantity');
            $specialcode->expiration = $request->input('expiration');
            $specialcode->payment_required = $request->input('payment_required');
            $specialcode->status = $request->input('status');
            $specialcode->usage_rule = $request->input('usage_rule');
            $specialcode->allowed_email = null;
            $specialcode->save();
            $specialcode->categories()->sync($request->input('categories'));
            $this->syncAllowedEmails($specialcode, $allowedEmails);
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return redirect()->route('specialcodes.index')->with('success', 'Special Code created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [
            'category_name' => 'specialcodes',
            'page_name' => 'specialcodes_edit',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];
        $specialcode = SpecialCode::with(['categories', 'allowedEmails'])->findOrFail($id);
        return view('pages.specialcodes.edit')->with($data)->with('specialcode', $specialcode)->with('categories', CategoryInscription::where('status', 'active')->where('name', '!=', 'Special Code')->orderBy('order')->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //validate update
        $this->validate($request, [
            'amount' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable',
            'quantity' => 'required|integer|min:1',
            'expiration' => 'required|date',
            'payment_required' => 'required|in:Si,No',
            'status' => 'required|in:Activo,Inactivo',
            'usage_rule' => 'required|in:limited,single_use,per_email',
            'allowed_emails' => 'required|string|max:50000',
            'categories' => 'required|array|min:1',
            'categories.*' => ['integer', 'distinct', Rule::exists('category_inscriptions', 'id')->where(function ($query) {
                $query->where('status', 'active')->where('name', '!=', 'Special Code');
            })],
        ]);
        $allowedEmails = $request->has('allowed_emails')
            ? RegistrationCoupon::normalizeAllowedEmails($request->input('allowed_emails'))
            : null;
        if (!$allowedEmails) {
            throw ValidationException::withMessages(['allowed_emails' => 'Enter at least one authorized email address.']);
        }
        if ($request->input('payment_required') === 'Si' && (float) $request->input('amount') <= 0) {
            throw ValidationException::withMessages(['amount' => 'Enter an amount greater than zero when payment is required.']);
        }

        //update
        DB::beginTransaction();
        try {
            $specialcode = SpecialCode::findOrFail($id);
            $specialcode->amount = $request->input('amount');
            $specialcode->description = $request->input('description');
            $specialcode->quantity = $request->input('usage_rule') === 'single_use' ? 1 : $request->input('quantity');
            $specialcode->expiration = $request->input('expiration');
            $specialcode->payment_required = $request->input('payment_required');
            $specialcode->status = $request->input('status');
            $specialcode->usage_rule = $request->input('usage_rule');
            if ($allowedEmails !== null) {
                $specialcode->allowed_email = null;
            }
            $specialcode->save();
            $specialcode->categories()->sync($request->input('categories'));
            if ($allowedEmails !== null) {
                $this->syncAllowedEmails($specialcode, $allowedEmails);
            }
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        //return edit page
        return redirect()->route('specialcodes.edit', $specialcode->id)->with('success', 'Special Code updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function syncAllowedEmails(SpecialCode $coupon, array $emails): void
    {
        $coupon->allowedEmails()->delete();
        foreach (array_chunk($emails, 200) as $chunk) {
            DB::table('special_code_allowed_emails')->insert(array_map(function ($email) use ($coupon) {
                return ['special_code_id' => $coupon->id, 'email' => $email];
            }, $chunk));
        }
    }

    public function validateSpecialCode(Request $request, RegistrationCoupon $coupons)
    {
        $request->validate([
            'code' => 'required|string|max:100',
            'category_inscription_id' => 'required|integer|exists:category_inscriptions,id',
            'email' => 'nullable|email',
            'inscription_id' => 'nullable|integer',
        ]);
        try {
            $inscription = auth()->user()->hasRole('Administrador') && $request->filled('inscription_id')
                ? Inscription::findOrFail($request->input('inscription_id'))
                : Inscription::where('user_id', auth()->id())->first();
            $accountEmail = $inscription && auth()->user()->hasRole('Administrador') && $request->filled('inscription_id')
                ? $inscription->user->email
                : auth()->user()->email;
            $formEmail = $request->input('email', $accountEmail);
            if (!auth()->user()->hasRole('Administrador') && strcasecmp(trim($formEmail), trim($accountEmail)) !== 0) {
                throw ValidationException::withMessages(['specialcode' => 'Use the email address of your account to apply this Special Code.']);
            }
            $coupon = $coupons->check(
                $request->input('code'),
                (int) $request->input('category_inscription_id'),
                $formEmail,
                $inscription ? $inscription->id : null
            );
        } catch (ValidationException $exception) {
            return response()->json(['success' => false, 'message' => $exception->errors()['specialcode'][0]]);
        }
        return response()->json([
            'success' => true,
            'message' => $coupon->description ?: 'Special Code applied.',
            'price' => (float) $coupon->amount,
            'amount' => (float) $coupon->amount,
            'payment_required' => $coupon->payment_required,
        ]);
    }
}
