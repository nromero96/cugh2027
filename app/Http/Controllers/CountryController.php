<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Country;
use App\Models\MemberInstitution;

use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    public function getByInstitution($id = null)
    {

        // Si no envían ID → listar todos
        if ($id) {
            $memberinstitution = MemberInstitution::find($id);

            if ($memberinstitution && $memberinstitution->country) {
                return Country::where('id', $memberinstitution->country)
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get();
            }
        }

        //Obtener country de la MemberInstitution
        // Buscar institución
        $memberinstitution = MemberInstitution::find($id);

        // Si no existe institución → listar todos
        if ($memberinstitution && $memberinstitution->country != null) {
            // Si existe → devolver solo su país
            $countries = Country::where('id', $memberinstitution->country)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

                return response()->json($countries);

        }


        $countries = Country::select('id', 'name')
                ->orderByRaw("CASE WHEN name = 'Perú' THEN 0 ELSE 1 END")
                ->orderBy('name')
                ->get();

            return response()->json($countries);

        
    }
}
