<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MemberInstitution;

class MemberInstitutionController extends Controller
{

    public function index()
    {
        $data = [
            'category_name' => 'member_institutions',
            'page_name' => 'member_institutions',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        //get member_institution
        $member_institutions = MemberInstitution::leftJoin('countries', 'member_institutions.country', '=', 'countries.id')
            ->select('member_institutions.*', 'countries.name as country_name')
            ->orderBy('member_institutions.name', 'asc')
            ->get();

        return view('pages.member_institutions.index', $data)->with('member_institutions', $member_institutions);
    }


    public function updateStatus(Request $request)
    {
        $institution = MemberInstitution::findOrFail($request->id);
        $institution->is_active = $request->status;
        $institution->save();

        return response()->json([
            'success' => true
        ]);
    }

}
