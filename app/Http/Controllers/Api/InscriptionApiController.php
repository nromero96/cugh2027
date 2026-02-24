<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inscription;
use App\Models\User;

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

        $params = [
            'status'               => $inscription->status,
            'forma_de_pago'        => $forma_de_pago ?? '',
            'dato_transferencia'   => '',
            'codigo_comercio'      => config('services.upch.commercial_code'),
            'codigo_tarifario'     => '',
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
}
