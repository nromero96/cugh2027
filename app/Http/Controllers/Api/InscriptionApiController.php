<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inscription;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class InscriptionApiController extends Controller
{
    public function showForUniversity(Request $request, $token)
    {
        // 🔐 Validar API KEY
        if ($request->header('X-API-KEY') != config('services.upch.webhook_key')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $inscription = Inscription::where('token', $token)->first();

        if (!$inscription) {
            return response()->json(['message' => 'Not found'], 404);
        }

        //Forma de pago
        if ($inscription->payment_method == 'Credit/Debit Card') {
            $forma_de_pago = '001';
        }else{
            $forma_de_pago = '002';
        }

            

            if($inscription->invoice_type == 'Factura'){
                $tipo_comprobante = 'F';
            } else {
                $tipo_comprobante = 'B';
            }

        $user = User::find($inscription->user_id);

        $codigo_tarifario = $this->getCodigoTarifario($inscription, $user);

        if($inscription->voucher_file != null){
            $url_voucher = asset('storage/uploads/voucher_file/' . $inscription->voucher_file);
        } else {
            $url_voucher = '';
        }

        $params = [
            'status'               => $inscription->status,
            'forma_de_pago'        => $forma_de_pago ?? '',
            'dato_transferencia'   => $url_voucher ?? '',
            'codigo_comercio'      => config('services.upch.commercial_code'),
            'codigo_tarifario'     => $codigo_tarifario ?? '',
            'moneda'               => 'USD',
            'monto'                => $inscription->total,
            'correo'               => $user->email,
            'nombre_completo'      => trim($user->name . ' ' . ($user->lastname ?? '')),
            'apellido_paterno'     => $user->second_lastname ?? '',
            'apellido_materno'     => '.',
            'codigo_pais'          => $user->phone_code,
            'numero_celular'       => $user->phone_number ?? '',
            'pais_origen'          => optional($user->residenceCountry)->name,
            'tipo_documento'       => $user->document_type ?? '',
            'numero_documento'     => $user->document_number ?? '',
            'tipo_comprobante'     => $tipo_comprobante ?? '',
            'tipo_doc_comp'        => $inscription->invoice_type_document ?? '',
            'numero_doc_comp'      => $inscription->invoice_ruc ?? '',
            'razon_social'         => $inscription->invoice_social_reason ?? '',
            'direccion_fiscal'     => $inscription->invoice_address ?? '',
            'numero_inscripcion'   => $inscription->id,
            'ciudad'               => $user->city ?? '',
            'correo_contacto'      => $user->email ?? '',
            'url_respuesta'        => config('services.upch.url_response_payment_data'),
        ];

        return response()->json($params, 200);
    }

    private function getCodigoTarifario($inscription, $user){
        $categoryId = $inscription->category_inscription_id ?? null;
        $priceType = $user->residenceCountry->price_type ?? null;

        // 🔹 Tipo país
        $countryType = ($priceType === 'High Income') ? 'HIC' : 'MIC';

        $codes = [
            1 => ['HIC' => '96044156', 'MIC' => '96044160'],
            2 => ['HIC' => '96044157', 'MIC' => '96044161'],
            3 => ['HIC' => '96044158', 'MIC' => '96044162'],
            4 => ['HIC' => '96044159', 'MIC' => '96044163'],
        ];

        return $codes[$categoryId][$countryType] ?? null;
    }
}
