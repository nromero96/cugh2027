<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Country;
use App\Models\CategoryInscription;

class CategoryInscriptionController extends Controller
{
    public function pricesByCountry(Request $request)
    {
        $request->validate([
            'country_id' => 'required|integer'
        ]);

        $country = Country::findOrFail($request->country_id);

        $categories = CategoryInscription::select(
            'id',
            'price',
            'price_low'
        )->get();

        $data = [];

        foreach ($categories as $cat) {
            if ($country->price_type === 'Middle Income') {
                $data[$cat->id] = $cat->price_low;
            } else { // High Income
                $data[$cat->id] = $cat->price;
            }
        }

        return response()->json($data);
    }

}
