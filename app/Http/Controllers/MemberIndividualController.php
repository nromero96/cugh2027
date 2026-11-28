<?php

namespace App\Http\Controllers;

use App\Models\MemberIndividual;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemberIndividualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $search = trim((string) request('search', ''));
        $members = MemberIndividual::query()
            ->when($search !== '', function ($query) use ($search) {
                foreach (preg_split('/\s+/u', mb_strtolower($search), -1, PREG_SPLIT_NO_EMPTY) as $word) {
                    $term = '%'.$word.'%';
                    $query->where(function ($matches) use ($term, $word) {
                        $matches->whereRaw('LOWER(first_name) LIKE ?', [$term])
                            ->orWhereRaw('LOWER(last_name) LIKE ?', [$term])
                            ->orWhereRaw('LOWER(email) LIKE ?', [$term]);
                        if (ctype_digit($word)) {
                            $matches->orWhere('id', (int) $word);
                        }
                    });
                }
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(25)
            ->withQueryString();

        $emails = $members->getCollection()->pluck('email')
            ->map(function ($email) { return mb_strtolower(trim($email)); })
            ->unique()->values()->all();

        $registrationsByEmail = empty($emails) ? collect() : DB::table('users')
            ->join('inscriptions', 'inscriptions.user_id', '=', 'users.id')
            ->whereIn(DB::raw('LOWER(users.email)'), $emails)
            ->select('users.email', 'users.is_cugh_member', 'users.cugh_membership_type')
            ->get()
            ->groupBy(function ($registration) { return mb_strtolower(trim($registration->email)); });

        $members->getCollection()->each(function ($member) use ($registrationsByEmail) {
            $registrations = $registrationsByEmail->get(mb_strtolower(trim($member->email)), collect());
            $member->has_registration = $registrations->isNotEmpty();
            $member->used_individual_membership = $registrations->contains(function ($registration) {
                return (int) $registration->is_cugh_member === 1
                    && $registration->cugh_membership_type === 'Individual Member';
            });
        });

        return view('pages.member_individuals.index', [
            'category_name' => 'member_individuals',
            'page_name' => 'member_individuals',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
            'members' => $members,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless($request->user()->hasRole('Administrador'), 403);

        $request->merge([
            'first_name' => trim((string) $request->input('first_name')) ?: null,
            'last_name' => trim((string) $request->input('last_name')) ?: null,
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);
        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        if (MemberIndividual::whereRaw('LOWER(email) = ?', [$validated['email']])->exists()) {
            throw ValidationException::withMessages(['email' => 'This email is already registered as an individual member.']);
        }

        $member = MemberIndividual::create([
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'is_active' => true,
        ]);

        return redirect()->route('member_individuals.index', ['search' => $member->email])
            ->with('success', 'Individual member added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MemberIndividual  $memberIndividual
     * @return \Illuminate\Http\Response
     */
    public function show(MemberIndividual $memberIndividual)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MemberIndividual  $memberIndividual
     * @return \Illuminate\Http\Response
     */
    public function edit(MemberIndividual $memberIndividual)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MemberIndividual  $memberIndividual
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MemberIndividual $memberIndividual)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MemberIndividual  $memberIndividual
     * @return \Illuminate\Http\Response
     */
    public function destroy(MemberIndividual $memberIndividual)
    {
        //
    }
}
