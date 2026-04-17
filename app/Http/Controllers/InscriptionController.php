<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Payment;
use App\Models\CategoryInscription;
use App\Models\Inscription;
use App\Models\TemporaryFile;
use App\Models\Accompanist;
use App\Models\Statusnote;
use App\Models\SpecialCode;
use App\Models\BeneficiarioBeca;
use App\Models\Country;
use App\Models\MemberInstitution;


use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;

///DB
use DB;


use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $iduser = \Auth::user()->id;

        $data = [
            'category_name' => 'inscriptions',
            'page_name' => 'inscriptions',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $listforpage = request()->query('listforpage') ?? 10;
        $search = request()->query('search');

        if (\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria') || \Auth::user()->hasRole('Hotelero') || \Auth::user()->hasRole('Check-in')) {

            $inscriptions = Inscription::leftJoin('category_inscriptions', 'inscriptions.category_inscription_id', '=', 'category_inscriptions.id')
                ->join('users', 'inscriptions.user_id', '=', 'users.id')
                ->leftJoin('countries', 'users.country', '=', 'countries.id')
                ->select('inscriptions.*', 'category_inscriptions.name as category_inscription_name', 'users.name as user_name', 'users.lastname as user_lastname', 'users.second_lastname as user_second_lastname', 'countries.name as user_country', 'users.email as user_email')
                ->where('inscriptions.status', '!=', 'Rechazado')
                ->where(function ($query) use ($search) {
                    if(strcasecmp($search, 'pendiente pagar') === 0){
                        $query->where('inscriptions.status', 'Pending')
                        ->where('inscriptions.payment_method', 'Credit/Debit Card')
                        ->where('inscriptions.total', '>', 0)
                        ->where(function ($subQuery) {
                            $subQuery->whereNull('inscriptions.special_code')
                                ->orWhere('inscriptions.special_code', '');
                        });
                    } else {
                        // Si la búsqueda comienza con #, buscar exactamente inscriptions.id
                        if (strpos($search, '#') === 0) {
                            $searchWithoutHash = ltrim($search, '#');
                            $query->where('inscriptions.id', $searchWithoutHash);
                        } else {
                            // Si no comienza con #, buscar cualquier coincidencia parcial
                            $query->where('inscriptions.id', 'LIKE', "%{$search}%");
                        }

                        // Búsqueda por nombre completo o primer nombre y primer apellido
                        $search = str_replace(' ', '%', $search);
                        $query->orWhereRaw('CONCAT(COALESCE(users.name, ""), " ", COALESCE(users.lastname, ""), " ", COALESCE(users.second_lastname, "")) LIKE ?', ["%{$search}%"]);

                        $query->orWhere('users.country', 'LIKE', "%{$search}%")
                            ->orWhere('category_inscriptions.name', 'LIKE', "%{$search}%")
                            ->orWhere('inscriptions.special_code', 'LIKE', "%{$search}%")
                            ->orWhere('inscriptions.status', 'LIKE', "%{$search}%")
                            ->orWhere('inscriptions.payment_method', 'LIKE', "%{$search}%")
                            ->orWhere('inscriptions.created_at', 'LIKE', "%{$search}%");
                    }
                })
                ->orderBy('inscriptions.id', 'desc')
                ->paginate($listforpage);
        } else {
            $inscriptions = Inscription::leftJoin('category_inscriptions', 'inscriptions.category_inscription_id', '=', 'category_inscriptions.id')
                ->join('users', 'inscriptions.user_id', '=', 'users.id')
                ->leftJoin('countries', 'users.country', '=', 'countries.id')
                ->select('inscriptions.*', 'category_inscriptions.name as category_inscription_name', 'users.name as user_name', 'users.lastname as user_lastname', 'users.second_lastname as user_second_lastname', 'countries.name as user_country')
                ->where('inscriptions.user_id', $iduser)
                ->orderBy('inscriptions.id', 'desc')
                ->paginate($listforpage);
        }
        

        return view('pages.inscriptions.index')->with($data)->with('inscriptions', $inscriptions);
    }

    public function indexAccompanists(){

        $data = [
            'category_name' => 'inscriptions',
            'page_name' => 'inscriptions_ccompanists',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $listforpage = request()->query('listforpage') ?? 10;
        $search = request()->query('search');

        //list inscriptions with accompanists
        $accompanists = Inscription::join('accompanists', 'inscriptions.accompanist_id', '=', 'accompanists.id')
            ->join('category_inscriptions', 'inscriptions.category_inscription_id', '=', 'category_inscriptions.id')
            ->select('accompanists.*', 'category_inscriptions.name as category_inscription_name', 'inscriptions.id as inscription_id', 'inscriptions.status as inscription_status', 'inscriptions.payment_method as inscription_payment_method', 'inscriptions.price_accompanist as inscription_price_accompanist', 'inscriptions.special_code as inscription_special_code')
            ->where('inscriptions.status', '!=', 'Rechazado')
            ->where(function ($query) use ($search) {
                // Si la búsqueda comienza con #, buscar exactamente inscriptions.id
                if (strpos($search, '#') === 0) {
                    $searchWithoutHash = ltrim($search, '#');
                    $query->where('inscriptions.id', $searchWithoutHash);
                } else {
                    // Si no comienza con #, buscar cualquier coincidencia parcial
                    $query->where('inscriptions.id', 'LIKE', "%{$search}%");
                }

                $query->orWhere('accompanists.accompanist_name', 'LIKE', "%{$search}%")
                    ->orWhere('accompanists.accompanist_numdocument', 'LIKE', "%{$search}%")
                    ->orWhere('accompanists.accompanist_solapin', 'LIKE', "%{$search}%")
                    ->orWhere('category_inscriptions.name', 'LIKE', "%{$search}%")
                    ->orWhere('inscriptions.status', 'LIKE', "%{$search}%")
                    ->orWhere('inscriptions.payment_method', 'LIKE', "%{$search}%")
                    ->orWhere('inscriptions.price_accompanist', 'LIKE', "%{$search}%")
                    ->orWhere('inscriptions.special_code', 'LIKE', "%{$search}%");
            })

            ->paginate($listforpage);
        
        return view('pages.inscriptions.accompanists')->with($data)->with('accompanists', $accompanists);


    }

    public function indexRejects(){
        $iduser = \Auth::user()->id;

        $data = [
            'category_name' => 'inscriptions',
            'page_name' => 'inscriptions_rejects',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        if (\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria') || \Auth::user()->hasRole('Hotelero')) {
            $inscriptions = Inscription::join('category_inscriptions', 'inscriptions.category_inscription_id', '=', 'category_inscriptions.id')
                ->join('users', 'inscriptions.user_id', '=', 'users.id')
                ->select('inscriptions.*', 'category_inscriptions.name as category_inscription_name', 'users.name as user_name', 'users.lastname as user_lastname', 'users.second_lastname as user_second_lastname', 'users.country as user_country')
                ->where('inscriptions.status', 'Rechazado')
                ->orderBy('inscriptions.id', 'desc')
                ->get();
        } else {
            $inscriptions = Inscription::join('category_inscriptions', 'inscriptions.category_inscription_id', '=', 'category_inscriptions.id')
                ->join('users', 'inscriptions.user_id', '=', 'users.id')
                ->select('inscriptions.*', 'category_inscriptions.name as category_inscription_name', 'users.name as user_name', 'users.lastname as user_lastname', 'users.second_lastname as user_second_lastname', 'users.country as user_country')
                ->where('inscriptions.user_id', $iduser)
                ->where('inscriptions.status', 'Rechazado')
                ->orderBy('inscriptions.id', 'desc')
                ->get();
        }
        

        return view('pages.inscriptions.rejects')->with($data)->with('inscriptions', $inscriptions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id = \Auth::user()->id;

        $data = [
            'category_name' => 'inscriptions',
            'page_name' => 'inscriptions_create',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $user = User::find($id);

        
        //get CategoryInscription
        $category_inscriptions = CategoryInscription::orderBy('order', 'asc')->get();

        return view('pages.inscriptions.create')->with($data)->with('user', $user)->with('category_inscriptions', $category_inscriptions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $amount_especialcode = 0;
        //si $request->category_inscription_id == 7 validar que exista el código especial
        if($request->category_inscription_id == 7){
            //get amount code special
            $specialcode = SpecialCode::where('code', $request->specialcode)->first();
            if($specialcode){
                $amount_especialcode = $specialcode->amount;
            }else{
                return redirect()->route('inscriptions.create')->with('error', 'The special code does not exist.');
            }
        }

        //get logged user id
        $iduser = \Auth::user()->id;

        //validar si el usuario ya tiene en la misma categoría
        $verificarinscription = Inscription::where('user_id', $iduser)
                                            ->where('category_inscription_id', $request->category_inscription_id)
                                            ->where('status', '!=', 'Rechazado')
                                            ->first();
        if($verificarinscription){
            return redirect()->route('inscriptions.create')->with('error', 'He already has an application in process in that category.');
        }

        //verificar si existe acompañante en la inscripcion, registrar y devolver id
        if($request->accompanist != ''){
            $accompanist = new Accompanist();
            $accompanist->accompanist_name = $request->accompanist_name;
            $accompanist->accompanist_typedocument = $request->accompanist_typedocument;
            $accompanist->accompanist_numdocument = $request->accompanist_numdocument;
            $accompanist->accompanist_solapin = $request->accompanist_solapin;
            $accompanist->save();
            $data_accompanist_id = $accompanist->id;
        }else{
            $data_accompanist_id = null;
        }

        //insert data
        $inscription = new Inscription();
        $inscription->user_id = $iduser;
        $inscription->category_inscription_id = $request->category_inscription_id;
        
        $category_inscription = CategoryInscription::find($request->category_inscription_id);
        
        //si $amount_especialcode es mayor a 0, poner el precio del código especial
        if($amount_especialcode > 0){
            $inscription->price_category = $amount_especialcode;
        }else{
            $inscription->price_category = $category_inscription->price;
        }

        if($request->accompanist != ''){
            $inscription->accompanist_id = $data_accompanist_id;
            $category_inscription_accompanist = CategoryInscription::where('name', 'Acompañante')->first();
            
            if($request->category_inscription_id == 9 || $request->category_inscription_id == 11){
                $inscription->price_accompanist = 0;
            }else{
                $inscription->price_accompanist = $category_inscription_accompanist->price;
            }
        }else{
            $inscription->accompanist_id = $data_accompanist_id;
            $inscription->price_accompanist = 0;
        }


        if($request->category_inscription_id == 9 || $request->category_inscription_id == 11){
            $inscription->total = 0;
        }else{
            $inscription->total = $inscription->price_category + $inscription->price_accompanist;
        }

        $inscription->special_code = $request->specialcode;
        $inscription->invoice = $request->invoice;
        $inscription->invoice_ruc = $request->invoice_ruc;
        $inscription->invoice_social_reason = $request->invoice_social_reason;
        $inscription->invoice_address = $request->invoice_address;
        $inscription->payment_method = $request->payment_method;
        $inscription->voucher_file = '';
        $inscription->save();

        $temporaryfile_document_file = TemporaryFile::where('folder', $request->document_file)->first();
        if($temporaryfile_document_file){
            Storage::move('public/uploads/tmp/'.$request->document_file.'/'.$temporaryfile_document_file->filename, 'public/uploads/document_file/'.$temporaryfile_document_file->filename);
            $inscription->document_file = $temporaryfile_document_file->filename;
            $inscription->save();
            rmdir(storage_path('app/public/uploads/tmp/'.$request->document_file));
            $temporaryfile_document_file->delete();
        }

        $temporaryfile_voucher_file = TemporaryFile::where('folder', $request->voucher_file)->first();
        if($temporaryfile_voucher_file){
            Storage::move('public/uploads/tmp/'.$request->voucher_file.'/'.$temporaryfile_voucher_file->filename, 'public/uploads/voucher_file/'.$temporaryfile_voucher_file->filename);
            $inscription->voucher_file = $temporaryfile_voucher_file->filename;
            $inscription->save();
            rmdir(storage_path('app/public/uploads/tmp/'.$request->voucher_file));
            $temporaryfile_voucher_file->delete();
        }

        if($request->payment_method == 'Transfer/Deposit'){

            $beneficiariobeca = BeneficiarioBeca::where('email', \Auth::user()->email)->first();
            if($beneficiariobeca && $request->category_inscription_id == '4' && $inscription->total == 0){
                $inscription->status = 'Pagado';
            }else{
                $inscription->status = 'Procesando';
            }

            $inscription->save();

            //send email
            $user = User::find($iduser);
            $datainscription = Inscription::join('category_inscriptions', 'inscriptions.category_inscription_id', '=', 'category_inscriptions.id')
            ->select('inscriptions.*', 'category_inscriptions.name as category_inscription_name')
            ->where('inscriptions.id', $inscription->id)
            ->first();
            $data = [
                'user' => $user,
                'datainscription' => $datainscription,
            ];

            Mail::to($user->email)
                ->cc(config('services.correonotificacion.inscripcion'))
                ->send(new \App\Mail\InscriptionCreated($data));


            //redirect
            return redirect()->route('inscriptions.index')->with('success', 'Inscripción realizada con éxito');
        } else if($request->payment_method == 'Card'){

            //verica si es beneficiario beca y el monto es 0
            $beneficiariobeca = BeneficiarioBeca::where('email', \Auth::user()->email)->first();
            if($beneficiariobeca && $inscription->total == 0){
                $inscription->status = 'Pendiente';
            }else{
                $inscription->status = 'Pendiente';
            }

            $inscription->save();

            //send email
            $user = User::find($iduser);
            $datainscription = Inscription::join('category_inscriptions', 'inscriptions.category_inscription_id', '=', 'category_inscriptions.id')
            ->select('inscriptions.*', 'category_inscriptions.name as category_inscription_name')
            ->where('inscriptions.id', $inscription->id)
            ->first();
            $data = [
                'user' => $user,
                'datainscription' => $datainscription,
            ];

            Mail::to($user->email)
                ->cc(config('services.correonotificacion.inscripcion'))
                ->send(new \App\Mail\InscriptionCreated($data));

            
            $tipo_comprobante = '';
            if($inscription->invoice == 'yes'){
                $tipo_comprobante = 'Factura';
            } else {
                $tipo_comprobante = 'Boleta';
            }

            $params = [
                'forma_de_pago'        => $inscription->payment_method ?? '',
                'dato_transferencia'   => 'URL_VOUCHER',
                'codigo_comercio'      => '',
                'codigo_tarifario'     => '',
                'moneda'               => 'USD',
                'monto'                => $inscription->total,
                'correo'               => $user->email,
                'nombre_completo'      => $user->name,
                'apellido_completo'    => $user->lastname ?? '', $user->second_lastname ?? '',
                'codigo_pais'          => $user->phone_code,
                'numero_celular'       => $user->phone_number ?? '',
                'pais_origen'          => $user->country ?? '',
                'tipo_documento'       => $user->document_type ?? '',
                'numero_documento'     => $user->document_number ?? '',
                'tipo_comprobante'     => $tipo_comprobante ?? '',
                'razon_social'         => $inscription->razon_social ?? '',
                'direccion_fiscal'     => $inscription->direccion_fiscal ?? '',
                'numero_inscripcion'   => $inscription->id,
                'ciudad'               => $user->city ?? '',
            ];

            $url = 'https://dev.dbtwhloljaupc.amplifyapp.com/?' . http_build_query($params);

            return redirect($url);

            //redirect to payment page with inscription id
            //return redirect()->route('inscriptions.paymentniubiz', ['inscription' => $inscription->id]);
        }

        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userAuth = \Auth::user();

        // Buscar inscripción SOLO para validación (ligera)
        $myinscription = Inscription::where('id', $id)
            ->where('user_id', $userAuth->id)
            ->first();

        // Validación de acceso
        if (
            $userAuth->hasRole('Administrador') ||
            $userAuth->hasRole('Secretaria') ||
            $userAuth->hasRole('Hotelero') ||
            $userAuth->hasRole('Check-in') ||
            $myinscription
        ) {

            $data = [
                'category_name' => 'inscriptions',
                'page_name' => 'inscriptions_show',
                'has_scrollspy' => 0,
                'scrollspy_offset' => '',
            ];

            // 🔹 Obtener inscripción con datos necesarios
            $inscription = Inscription::leftJoin('category_inscriptions', 'inscriptions.category_inscription_id', '=', 'category_inscriptions.id')
                ->select(
                    'inscriptions.*',
                    'category_inscriptions.name as category_inscription_name'
                )
                ->where('inscriptions.id', $id)
                ->first();

            // 🔹 Obtener usuario por separado (más limpio y escalable)
            $user = User::leftJoin('countries as un', 'users.nationality', '=', 'un.id')
                ->leftJoin('countries as uc', 'users.country', '=', 'uc.id')
                ->leftJoin('member_institutions', 'users.cugh_member_institution', '=', 'member_institutions.id')
                ->select(
                    'users.*',
                    'un.name as user_nationality',
                    'uc.name as user_country',
                    'member_institutions.name as cugh_member_institution_name'
                )
                ->where('users.id', $inscription->user_id)
                ->first();

            // 🔹 Listado de pagos
            $paymentcards = Payment::where('inscription_id', $id)
                ->orderBy('id', 'desc')
                ->get();

            // 🔹 Notas de estado
            $statusnotes = StatusNote::where('inscription_id', $id)
                ->orderBy('id', 'desc')
                ->get();

            return view('pages.inscriptions.show')
                ->with($data)
                ->with('inscription', $inscription)
                ->with('user', $user)
                ->with('paymentcards', $paymentcards)
                ->with('statusnotes', $statusnotes);

        } else {
            return redirect()->route('inscriptions.index')
                ->with('error', 'Permission denied, you do not have permission to view this inscription.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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


    public function registerMyInscription(){

        $user = auth()->user();
        $id = $user->id;

        $data = [
            'category_name' => 'inscriptions',
            'page_name' => 'inscriptions_myinscription',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        //get CategoryInscription
        $category_inscriptions = CategoryInscription::orderBy('order', 'asc')->get();
        //List Country
        $countries = Country::orderByRaw("CASE WHEN name = 'Perú' THEN 0 ELSE 1 END, name ASC")->get();
        //List Member Institution
        $memberinstitutions = MemberInstitution::all();

        $user = User::find($id);

        //Obtener la inscripción del usuario
        $myinscription = $user->inscription;

        //List of payment cards
        $paymentcards = Payment::where('inscription_id', $myinscription->id)->orderBy('id', 'desc')->get();

        //solo los roles de Administrador y Secretaria pueden ver esta vista
        return view('pages.inscriptions.my-inscription')->with($data)->with('category_inscriptions', $category_inscriptions)->with('countries', $countries)->with('user', $user)->with('memberinstitutions', $memberinstitutions)->with('myinscription', $myinscription)->with('paymentcards', $paymentcards);
    }

    public function storeMyInscription(Request $request){

        $action = $request->input('action');

        //get logged user id
        $iduser = \Auth::user()->id;

        Log::info('Datos de la inscripción: '.json_encode($request->all()));

        
        if ($action === 'register') {

            // VALIDACIÓN COMPLETA
            $rules = [
                //data user
                'salutation' => 'required|string',
                'name' => 'required|string',
                'lastname' => 'nullable|string',
                'second_lastname' => 'required|string',
                'degrees' => 'required|string',
                'other_degrees' => 'nullable|string',
                'is_cugh_member' => 'required|string',
                'cugh_member_institution' => 'nullable|string',
                'job_title' => 'required|string',
                'email' => 'required|email',
                'cc_email' => 'nullable|email',
                'document_type' => 'required|string',
                'document_number' => 'required|string',
                'nationality' => 'required|string',
                'gender' => 'required|string',
                'occupation' => 'required|string',
                'occupation_other' => 'nullable|string',
                'workplace' => 'required|string',
                'address' => 'required|string|max:50',
                'city' => 'required|string',
                'state' => 'required|string',
                'country' => 'required|string',
                'work_phone_code' => 'nullable|string',
                'work_phone_code_city' => 'nullable|string',
                'work_phone_number' => 'nullable|string',
                'phone_code' => 'required|string',
                'phone_number' => 'required|string',
                'whatsapp_code' => 'nullable|string',
                'whatsapp_number' => 'nullable|string',
                'solapin_name' => 'required|string',
                'solapin_lastname' => 'required|string',

                //Questionnaire Data
                'sector' => 'required|array',
                'other_sector' => 'nullable|string',

                'area_of_work' => 'required|array',
                'other_area_of_work' => 'nullable|string',

                'how_did_you_hear_about' => 'required|array',
                'other_how_did_you_hear_about' => 'nullable|string',

                'why_attending' => 'required|array',
                'other_why_attending' => 'nullable|string',

                'ability_to_present_work' => 'nullable|string',

                'how_is_your_attendance_funded' => 'required|array',
                'other_how_is_your_attendance_funded' => 'nullable|string',

                'your_areas_of_focus_in_global_health' => 'required|array',
                'other_your_areas_of_focus_in_global_health' => 'nullable|string',

                'obstacles_to_attending_cughs_conferences' => 'required|array',
                'other_obstacles_to_attending_cughs_conferences' => 'nullable|string',

                'receive_news_and_updates' => 'required|string',
                'contact_info' => 'required|string',
                'oral_poster_abstract_presenter' => 'required|string',
                'panel_presenter_moderator' => 'required|string',

                //data inscription
                'category_inscription_id' => 'required|numeric',
                'invoice' => 'required|string',
                'invoice_type' => 'required|string',
                'invoice_type_document' => 'required|string',
                'invoice_ruc' => 'required|string',
                'invoice_social_reason' => 'required|string',
                'invoice_address' => 'required|string|max:50',
                'payment_method' => 'required|string',
            ];

        } else {

            // VALIDACIÓN PARA SAVE (solo formato, nada obligatorio)
            $rules = [
                'salutation' => 'nullable|string',
                'name' => 'nullable|string',
                'lastname' => 'nullable|string',
                'second_lastname' => 'nullable|string',
                'degrees' => 'nullable|string',
                'other_degrees' => 'nullable|string',
                'is_cugh_member' => 'required|string',
                'cugh_member_institution' => 'nullable|string',
                'job_title' => 'nullable|string',
                'email' => 'required|email',
                'cc_email' => 'nullable|email',
                'document_type' => 'required|string',
                'document_number' => 'required|string',
                'nationality' => 'nullable|string',
                'gender' => 'nullable|string',
                'occupation' => 'nullable|string',
                'occupation_other' => 'nullable|string',
                'workplace' => 'nullable|string',
                'address' => 'nullable|string|max:50',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'country' => 'nullable|string',
                'work_phone_code' => 'nullable|string',
                'work_phone_code_city' => 'nullable|string',
                'work_phone_number' => 'nullable|string',
                'phone_code' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'whatsapp_code' => 'nullable|string',
                'whatsapp_number' => 'nullable|string',
                'solapin_name' => 'nullable|string',
                'solapin_lastname' => 'nullable|string',

                //Questionnaire Data
                'sector' => 'nullable|array',
                'other_sector' => 'nullable|string',

                'area_of_work' => 'nullable|array',
                'other_area_of_work' => 'nullable|string',

                'how_did_you_hear_about' => 'nullable|array',
                'other_how_did_you_hear_about' => 'nullable|string',

                'why_attending' => 'nullable|array',
                'other_why_attending' => 'nullable|string',

                'ability_to_present_work' => 'nullable|string',

                'how_is_your_attendance_funded' => 'nullable|array',
                'other_how_is_your_attendance_funded' => 'nullable|string',

                'your_areas_of_focus_in_global_health' => 'nullable|array',
                'other_your_areas_of_focus_in_global_health' => 'nullable|string',

                'obstacles_to_attending_cughs_conferences' => 'nullable|array',
                'other_obstacles_to_attending_cughs_conferences' => 'nullable|string',

                'receive_news_and_updates' => 'nullable|string',
                'contact_info' => 'nullable|string',
                'oral_poster_abstract_presenter' => 'nullable|string',
                'panel_presenter_moderator' => 'nullable|string',

                //data inscription
                'category_inscription_id' => 'nullable|numeric',
                'invoice' => 'required|string',
                'invoice_type' => 'required|string',
                'invoice_type_document' => 'nullable|string',
                'invoice_ruc' => 'nullable|string',
                'invoice_social_reason' => 'nullable|string',
                'invoice_address' => 'nullable|string|max:50',
                'payment_method' => 'nullable|string',
            ];
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {
            $country_inscription = Country::find($request->country);

            // Actualizar usuario
            $user = User::find($iduser);
            $user->salutation = $request->salutation;
            $user->name = $request->name;
            $user->lastname = $request->lastname;
            $user->second_lastname = $request->second_lastname;
            $user->degrees = $request->degrees;
            $user->other_degrees = $request->other_degrees;
            $user->is_cugh_member = $request->is_cugh_member;
            $user->cugh_member_institution = $request->cugh_member_institution;
            $user->job_title = $request->job_title;
            $user->email = $request->email;
            $user->cc_email = $request->cc_email;
            $user->document_type = $request->document_type;
            $user->document_number = $request->document_number;
            $user->nationality = $request->nationality;
            $user->gender = $request->gender;
            $user->occupation = $request->occupation;
            $user->occupation_other = $request->occupation_other;
            $user->workplace = $request->workplace;
            $user->address = $request->address;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->country = $request->country;
            $user->work_phone_code = $request->phone_code;
            $user->work_phone_code_city = $request->work_phone_code_city;
            $user->work_phone_number = $request->phone_number;
            $user->phone_code = $request->phone_code;
            $user->phone_number = $request->phone_number;
            $user->whatsapp_code = $request->whatsapp_code;
            $user->whatsapp_number = $request->whatsapp_number;
            $user->solapin_name = $request->solapin_name;
            $user->solapin_lastname = $request->solapin_lastname;

            //Questionnaire Data
            $user->sector = $request->sector ?? [];
            $user->other_sector = $request->other_sector;

            $user->area_of_work = $request->area_of_work ?? [];
            $user->other_area_of_work = $request->other_area_of_work;

            $user->how_did_you_hear_about = $request->how_did_you_hear_about ?? [];
            $user->other_how_did_you_hear_about = $request->other_how_did_you_hear_about;

            $user->why_attending = $request->why_attending ?? [];
            $user->other_why_attending = $request->other_why_attending;

            $user->ability_to_present_work = $request->ability_to_present_work;

            $user->how_is_your_attendance_funded = $request->how_is_your_attendance_funded ?? [];
            $user->other_how_is_your_attendance_funded = $request->other_how_is_your_attendance_funded;

            $user->your_areas_of_focus_in_global_health = $request->your_areas_of_focus_in_global_health ?? [];
            $user->other_your_areas_of_focus_in_global_health = $request->other_your_areas_of_focus_in_global_health;

            $user->obstacles_to_attending_cughs_conferences = $request->obstacles_to_attending_cughs_conferences ?? [];
            $user->other_obstacles_to_attending_cughs_conferences = $request->other_obstacles_to_attending_cughs_conferences;

            $user->receive_news_and_updates = $request->receive_news_and_updates;
            $user->contact_info = $request->contact_info;
            $user->oral_poster_abstract_presenter = $request->oral_poster_abstract_presenter;
            $user->panel_presenter_moderator = $request->panel_presenter_moderator;

            $user->save();

            //Buscar inscripción del usuario para actualizarla
            $inscription = Inscription::where('user_id', $iduser)->first();
            
            // Actualizar inscripción del usuario
            $inscription = Inscription::find($inscription->id);
            $inscription->user_id = $iduser;
            $inscription->category_inscription_id = $request->category_inscription_id;

            
            $categoryInscription = CategoryInscription::find($request->category_inscription_id);

            if ($categoryInscription) {
                // Si se encuentra la categoría
                $price_category = $country_inscription->price_type === 'Middle Income'
                    ? $categoryInscription->price_low
                    : $categoryInscription->price;
            } else {
                // Si no llega category_inscription_id o no se encuentra
                $price_category = 0;
            }

            $inscription->price_category = $price_category;
            $inscription->total = $price_category;


            $inscription->special_code = $request->specialcode;
            $inscription->invoice = $request->invoice;
            $inscription->invoice_type = $request->invoice_type;
            $inscription->invoice_type_document = $request->invoice_type_document;
            $inscription->invoice_ruc = $request->invoice_ruc;
            $inscription->invoice_social_reason = $request->invoice_social_reason;
            $inscription->invoice_address = $request->invoice_address;
            $inscription->payment_method = $request->payment_method;

            $inscription->save();

            // Manejo de documentos temporales
            $documentFile = trim($request->document_file, '[]"');
            $temporaryfile_document_file = TemporaryFile::where('folder', $documentFile)->first();
            if ($temporaryfile_document_file) {
                Storage::move('public/uploads/tmp/'.$documentFile.'/'.$temporaryfile_document_file->filename, 'public/uploads/document_file/'.$temporaryfile_document_file->filename);
                $inscription->document_file = $temporaryfile_document_file->filename;
                $inscription->save();
                rmdir(storage_path('app/public/uploads/tmp/'.$documentFile));
                $temporaryfile_document_file->delete();
            }

            $voucherFile = trim($request->voucher_file, '[]"');
            $temporaryfile_voucher_file = TemporaryFile::where('folder', $voucherFile)->first();
            if ($temporaryfile_voucher_file) {
                Storage::move('public/uploads/tmp/'.$voucherFile.'/'.$temporaryfile_voucher_file->filename, 'public/uploads/voucher_file/'.$temporaryfile_voucher_file->filename);
                $inscription->voucher_file = $temporaryfile_voucher_file->filename;
                $inscription->save();
                rmdir(storage_path('app/public/uploads/tmp/'.$voucherFile));
                $temporaryfile_voucher_file->delete();
            }

            if($action == 'save'){
                $inscription->status = 'Draft';
                $inscription->save();
                DB::commit();
                return redirect()->route('inscriptions.myinscription')->with('success', 'Draft saved successfully. Registration is not completed yet.');
            }else{
                if ($request->payment_method == 'Bank Transfer/Wire' || $request->payment_method == 'none') {
                    $inscription->status = 'Processing';
                    $inscription->save();

                    // Enviar correo
                    $user = User::find($iduser);
                    $datainscription = Inscription::join('category_inscriptions', 'inscriptions.category_inscription_id', '=', 'category_inscriptions.id')
                        ->select('inscriptions.*', 'category_inscriptions.name as category_inscription_name')
                        ->where('inscriptions.id', $inscription->id)
                        ->first();
                    $data = [
                        'user' => $user,
                        'datainscription' => $datainscription,
                    ];

                    Mail::to($user->email)
                        ->cc(config('services.correonotificacion.inscripcion'))
                        ->send(new \App\Mail\InscriptionCreated($data));

                    DB::commit();

                } else if ($request->payment_method == 'Credit/Debit Card') {
                    $inscription->status = 'Pending';
                    $inscription->save();
                    DB::commit();
                }
            }


            //Send Data to UPCH
            $url = config('services.upch.url_send_data').'/' . $inscription->token;
            return redirect($url);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar inscripción: '.$e->getMessage());
            return redirect()->route('inscriptions.myinscription')->with('error', 'Error al registrar inscripción');
        }
    }


    public function paymentResult(Request $request)
    {
        $numeroInscripcion = $request->get('numero_inscripcion');
        $estadoPago        = $request->get('estado_pago');
        $mensaje           = $request->get('mensaje_operacion');
        $numeroOperacion   = $request->get('numero_operacion');
        $tarjetaRecortada  = $request->get('numero_tarjeta_recortado');

        if (!$numeroInscripcion || !$numeroOperacion) {
            abort(400, 'Incomplete payment data');
        }

        $inscription = Inscription::findOrFail($numeroInscripcion);



        return view('pages.inscriptions.payment-result', [
            'inscription_id' => $inscription->id,
            'estado'         => $estadoPago,
            'mensaje'        => $mensaje,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            // Obtener la inscripción actual
            $inscription = Inscription::findOrFail($id);

            // Validación de datos (ajusta estas reglas según tus necesidades)
            $validatedData = $request->validate([
                'action' => 'required',
                'note' => 'nullable|string',
            ]);

            // Insertar la nota de estado
            StatusNote::create([
                'inscription_id' => $id,
                'action' => "Changed from '{$inscription->status}' to '{$validatedData['action']}'",
                'note' => $validatedData['note'] ?? 'No notes',
                'user_id' => auth()->id(),
            ]);

            // Actualizar el estado de la inscripción después de registrar la nota
            $inscription->update([
                'status' => $validatedData['action'],
                'updated_at' => now(),
            ]);

            return redirect()->route('inscriptions.show', ['inscription' => $id])->with('success', 'Updated status successfully.');
        } catch (\Exception $e) {
            // Manejo de errores
            return redirect()->back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function requestComprobante(Request $request, $id)
    {
        // Validar si el usuario logueado es Administrador o Secretaria
        if (\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria')) {
            // Obtener la inscripción
            $inscription = Inscription::find($id);

            // Actualizar status_compr = Pendiente si el status es Ninguna
            if ($inscription->status_compr == 'Ninguna') {
                $inscription->status_compr = 'Pendiente';
                $inscription->save();
            } else {
                // Devolver un mensaje de error en formato JSON
                return response()->json(['error' => 'Ya se solicitó el comprobante'], 403);
            }

            // Devolver "ok" como indicador de éxito
            return response()->json(['status' => 'ok']);
        } else {
            // Devolver un mensaje de error en formato JSON
            return response()->json(['error' => 'No tiene permisos para solicitar comprobante'], 403);
        }
    }

    public function exportExcelInscriptions()
    {

        //if user is admin or secretary
        if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria')){
            return Excel::download(new \App\Exports\ExporInscriptions, 'inscriptions.xlsx');
        }else{
            echo 'No tiene permisos para exportar';
            exit;
        }

        
    }

}
