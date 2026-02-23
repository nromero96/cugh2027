<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\Inscription;
use App\Models\User;

use Illuminate\Support\Facades\Mail;



use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request){
        
        // 🔐 Validar API KEY
        if ($request->header('X-API-KEY') != config('services.upch.webhook_key')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $numeroInscripcion = $request->numero_inscripcion;
        $estadoPago        = $request->estado_pago;
        $mensaje           = $request->mensaje_operacion;
        $numeroOperacion   = $request->numero_operacion;
        $tarjetaRecortada  = $request->numero_tarjeta_recortado;

        if (!$numeroInscripcion || !$numeroOperacion) {
            return response()->json(['message' => 'Incomplete payment data'], 400);
        }

        $inscription = Inscription::find($numeroInscripcion);

        if (!$inscription) {
            return response()->json(['message' => 'Inscription not found'], 404);
        }

        // Validar que la inscripcion esta en status Pending
        if ($inscription->status !== 'Pending') {
            return response()->json(['message' => 'Inscription is not in Pending status'], 400);
        }

        // 🔁 Evitar duplicados
        $payment = Payment::where('purchasenumber', $numeroOperacion)->first();

        if (!$payment) {

            $payment = Payment::create([
                'inscription_id'     => $inscription->id,
                'user_id'            => $inscription->user_id,
                'action_description' => $estadoPago . ': ' . $mensaje,
                'purchasenumber'     => $numeroOperacion,
                'card_brand'         => '',
                'card_number'        => $tarjetaRecortada,
                'amount'             => $inscription->total,
                'currency'           => 'USD',
                'transaction_date'   => now(),
                'status_payment'     => $estadoPago,
                'raw_response'       => json_encode($request->all()),
            ]);
        }

        // ✅ Actualizar inscripción si está autorizado
        if ($estadoPago === 'AUTORIZADO' && $inscription->status !== 'Paid') {
            $inscription->update([
                'status' => 'Paid'
            ]);

            //Enviar correo
            $user = User::find($inscription->user_id);

            $data = [
                'user' => $user,
                'datainscription' => $inscription
            ];

            Mail::to($user->email)
                    ->cc(config('services.correonotificacion.inscripcion'))
                    ->send(new \App\Mail\InscriptionCreated($data));
        }

        return response()->json([
            'message' => 'Payment registered successfully',
        ], 200);
        

    }
}
