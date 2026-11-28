<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Country;
use App\Models\MemberInstitution;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MemberInstitutionController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $data = [
            'category_name' => 'member_institutions',
            'page_name' => 'member_institutions',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $search = trim((string) request('search', ''));
        $registrationCounts = $this->institutionRegistrations()
            ->select('users.cugh_member_institution')
            ->selectRaw('COUNT(*) as registrations_count')
            ->groupBy('users.cugh_member_institution');

        $member_institutions = MemberInstitution::leftJoin('countries', 'member_institutions.country', '=', 'countries.id')
            ->leftJoinSub($registrationCounts, 'registration_counts', function ($join) {
                $join->on('registration_counts.cugh_member_institution', '=', 'member_institutions.id');
            })
            ->select('member_institutions.*', 'countries.name as country_name')
            ->selectRaw('COALESCE(registration_counts.registrations_count, 0) as registrations_count')
            ->when($search !== '', function ($query) use ($search) {
                $term = '%'.mb_strtolower($search).'%';
                $query->where(function ($matches) use ($search, $term) {
                    $matches->whereRaw('LOWER(member_institutions.name) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(countries.name) LIKE ?', [$term]);
                    if (ctype_digit($search)) {
                        $matches->orWhere('member_institutions.id', (int) $search);
                    }
                });
            })
            ->orderBy('member_institutions.name', 'asc')
            ->get();

        return view('pages.member_institutions.index', $data)
            ->with('member_institutions', $member_institutions)
            ->with('countries', Country::orderBy('name')->get(['id', 'name']))
            ->with('search', $search);
    }

    public function registrationEmails($id)
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
        MemberInstitution::findOrFail($id);

        $emails = $this->institutionRegistrations()
            ->where('users.cugh_member_institution', $id)
            ->orderBy('users.email')
            ->pluck('users.email');

        return response()->json(['emails' => $emails]);
    }

    private function institutionRegistrations()
    {
        return DB::table('inscriptions')
            ->join('users', 'inscriptions.user_id', '=', 'users.id')
            ->where('users.is_cugh_member', 1)
            ->where(function ($membership) {
                $membership->where('users.cugh_membership_type', 'Institutional Member')
                    ->orWhereNull('users.cugh_membership_type');
            })
            ->whereNotNull('users.cugh_member_institution');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasRole('Administrador'), 403);

        $request->merge(['name' => trim((string) $request->input('name'))]);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('member_institutions', 'name')->where('country', $request->input('country'))],
            'country' => ['nullable', 'integer', 'exists:countries,id'],
        ], [
            'name.unique' => 'This institution is already registered.',
        ]);

        $institution = MemberInstitution::create([
            'name' => $validated['name'],
            'country' => $validated['country'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('member_institutions.index', ['search' => $institution->name])
            ->with('success', 'Member institution added successfully.');
    }


    public function updateStatus(Request $request)
    {
        abort_unless($request->user()->hasRole('Administrador'), 403);
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:member_institutions,id'],
            'status' => ['required', 'boolean'],
        ]);

        $institution = MemberInstitution::findOrFail($validated['id']);
        $institution->is_active = $validated['status'];
        $institution->save();

        return response()->json([
            'success' => true
        ]);
    }

}
