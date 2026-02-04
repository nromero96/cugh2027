@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="statbox widget box box-shadow ficha-inscripcion">
                    <div class="widget-header px-3">
                        <div class="row g-3">
                            <div class="col-md-8 py-3">
                                <h4 class="px-0 py-0">
                                    {{__("Registration")}} # {{ $inscription->id }}
                                </h4>
                            </div>
                            <div class="col-md-4 py-3 text-end">

                                <a href="#" class="btn btn-primary px-1 py-1 btnprintficha" style="margin-top: -6px;">
                                    <svg width="14" height="14" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 9V2h12v7"></path>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                        <path d="M6 14h12v8H6z"></path>
                                    </svg>
                                </a>

                                @if($inscription->status == 'Pagado')
                                    <span class="badge badge-light-success">{{ $inscription->status .' ('.$inscription->payment_method.')' }}</span>
                                @elseif ($inscription->status == 'Procesando')
                                    <span class="badge badge-light-info">{{ $inscription->status .' ('.$inscription->payment_method.')' }}</span>
                                @elseif ($inscription->status == 'Pending Payment')
                                    <span class="badge badge-light-warning">{{ $inscription->status .' ('.$inscription->payment_method.')' }}</span>
                                @elseif ($inscription->status == 'Rechazado')
                                    <span class="badge badge-light-danger">{{ $inscription->status .' ('.$inscription->payment_method.')' }}</span>
                                @endif
                                <span class="d-block">{{ $inscription->created_at }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">First Name:</label><br>
                                <span class="bx-text">{{ $inscription->user_name }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Middle Name:</label><br>
                                <span class="bx-text">{{ $inscription->user_lastname }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Last Name:</label><br>
                                <span class="bx-text">{{ $inscription->user_second_lastname }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Document Type:</label><br>
                                <span class="bx-text">{{ $inscription->user_document_type }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Document Number:</label><br>
                                <span class="bx-text">{{ $inscription->user_document_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Nationality:</label><br>
                                <span class="bx-text">{{ $inscription->user_nationality }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Gender:</label><br>
                                <span class="bx-text">{{ $inscription->user_gender }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Occupation:</label><br>
                                <span class="bx-text">{{ $inscription->user_occupation }}</span>
                            </div>

                            <div class="col-md-4 @if($inscription->user_occupation != 'Other') d-none @else @endif" id="occupation_other">
                                <label class="form-label fw-bold mb-0">Other Occupation:</label><br>
                                <span class="bx-text">{{ $inscription->user_occupation_other }}</span>
                            </div>

                            <div class="col-md-12">
                                    <hr class="mt-1 mb-0">
                                    <div class="row">
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">Workplace:</label><br>
                                            <span class="bx-text">{{ $inscription->user_workplace }}</span>
                                        </div>
                                        <div class="col-md-8 mt-3">
                                            <label class="form-label fw-bold mb-0">Workplace Address:</label><br>
                                            <span class="bx-text">{{ $inscription->user_address }}</span>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">City:</label><br>
                                            <span class="bx-text">{{ $inscription->user_city }}</span>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">State:</label><br>
                                            <span class="bx-text">{{ $inscription->user_state }}</span>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">Country:</label><br>
                                            <span class="bx-text">{{ $inscription->user_country }}</span>
                                        </div>
                                    </div>
                                    <hr class="mt-3 mb-1">
                            </div>


                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Phone:</label><br>
                                <span class="bx-text">{{ $inscription->user_phone_code.' '.$inscription->user_phone_code_city.' '.$inscription->user_phone_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">WhatsApp:</label><br>
                                <span class="bx-text">{{ $inscription->user_whatsapp_code.' '.$inscription->user_whatsapp_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">E-mail:</label><br>
                                <span class="bx-text">{{ $inscription->user_email }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Conference badge:</label><br>
                                <span class="bx-text">
                                    {{ $inscription->user_solapin_name }} {{ $inscription->user_solapin_lastname }}
                                    <a href="{{ route('gafetes.gafeteforparticipant', $inscription->id) }}" class="float-end px-1 py-0" target="_blank">
                                        <svg width="17" height="17" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <path d="M12 9a3 3 0 1 0 0 6 3 3 0 1 0 0-6z"></path>
                                        </svg>
                                    </a>
                                </span>
                            </div>

                            <div class="col-md-12">
                                <hr class="my-0">
                            </div>

                            <div class="col-md-12">
                                <h6>
                                    Registration details

                                    @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                                        <a href="{{ route('inscriptions.edit', $inscription->id) }}" class="btn btn-light-primary p-0 btn-sm ms-2 btneditinsc">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                              </svg>
                                        </a>
                                    @endif
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col"><b>Descripción</b></th>
                                                <th scope="col" width="105px"><b>Information</b></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                    <td>
                                                        Category
                                                    </td>
                                                    <td>
                                                        <b>{{ $inscription->category_inscription_name }}</b>
                                                        @if($inscription->special_code != '')
                                                            <br><small class="text-info" style="font-size: 11px;">{{ $inscription->special_code }}</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Registration fee
                                                    </td>
                                                    <td>
                                                        <b>US$ {{ $inscription->price_category }}</b>
                                                    </td>
                                                </tr>
                                            <tr class="table-secondary">
                                                <td><b>Total</b></td>
                                                <td><b>US$ {{ $inscription->total }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                @if ($inscription->document_file != null || $inscription->document_file != '')
                                    <div id="dv_document_file">
                                        <label class="form-label mt-3">
                                        <span class="fw-bold">Proof document of category. ({{ $inscription->category_inscription_name }}):</span></label><br>
                                        <div class="mt-1">
                                            <a href="{{ asset('storage/uploads/document_file').'/'.$inscription->document_file}}" class="badge badge-light-primary text-start me-2 bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-bs-original-title="Download" target="_blank">
                                                {{ $inscription->document_file }}
                                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7 10 5 5 5-5"></path><path d="M12 15V3"></path></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endif

                            </div>

                            @if($inscription->invoice == 'yes')
                            <div class="col-md-12">
                                <div class="card px-3 py-3">
                                    <label for="" class="form-label fw-bold">
                                        Billing information:
                                    </label>

                                    @if($inscription->user_country == 'Peru')
                                        <label for="" class="form-label fw-bold">
                                            {{$inscription->invoice_type}}
                                        </label>
                                    @endif

                                    <div class="row mt-1" id="dv_invoice_info">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold mb-0">Name/Entity:</label><br>
                                            <span class="bx-text">{{ $inscription->invoice_social_reason }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold mb-0">Tax ID (RUC):</label><br>
                                            <span class="bx-text">{{ $inscription->invoice_ruc }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold mb-0">Business Address:</label><br>
                                            <span class="bx-text">{{ $inscription->invoice_address }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @endif

                            <div class="col-md-12">
                                <div class="card px-3 py-3">
                                    <label for="" class="form-label fw-bold mb-0">Payment Method:</label>
                                    <div class="">
                                        {{ $inscription->payment_method }}
                                    </div>
                                    @if ($inscription->payment_method == 'Bank Transfer/Wire')
                                        <div class="row mt-1">
                                            <div class="col-md-12">
                                                <div class="mt-1">
                                                    <a href="{{ asset('storage/uploads/voucher_file').'/'.$inscription->voucher_file}}" class="badge badge-light-primary text-start me-2 bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-bs-original-title="Descargar" target="_blank">
                                                        {{ $inscription->voucher_file }}
                                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7 10 5 5 5-5"></path><path d="M12 15V3"></path></svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($inscription->payment_method == 'Credit/Debit Card' && $paymentcard != null)
                                    <div class="row mt-1">
                                        <div class="col-2">
                                            <label class="form-label fw-bold mb-0"># Transaction number:</label><br>
                                            <span class="bx-text">{{ $paymentcard->purchasenumber }}</span>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label fw-bold mb-0">Card:</label><br>
                                            <span class="bx-text">{{ $paymentcard->card_brand }}</span>
                                        </div>
                                        <div class="col-3">
                                            <label class="form-label fw-bold mb-0">Card #:</label><br>
                                            <span class="bx-text">{{ $paymentcard->card_number }}</span>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label fw-bold mb-0">Amount:</label><br>
                                            <span class="bx-text">{{ $paymentcard->amount.' '.$paymentcard->currency }}</span>
                                        </div>
                                        <div class="col-3">
                                            @php
                                                $carbonTDate = \Carbon\Carbon::createFromFormat('ymdHis', $paymentcard->transaction_date);
                                                $tansactionDate = $carbonTDate->format('Y-m-d H:i:s');
                                            @endphp
                                            <label class="form-label fw-bold mb-0">Transaction Date:</label><br>
                                            <span class="bx-text">{{ $tansactionDate }}</span>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <span class="bx-text">{{ $paymentcard->action_description }}</span>
                                        </div>
                                    </div>
                                    @endif

                                </div>
                            </div>


                            <div class="col-md-7">

                                @if ($inscription->status_compr != 'Informado' && (\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria')))
                                    <div class="card px-3 py-3 bg-primary mb-2 actionstatus">
                                        <label class="form-label mb-1 text-white"><span class="fw-bold">{{ __('Estado de la inscripción') }}</span>: <span>({{ $inscription->status }})</span></label>
                                        <form class="row" action="{{ route('inscriptions.updatestatus', ['id' => $inscription->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-4">
                                                <select name="action" id="action" class="form-control">
                                                    <option value="Pendiente" @if ($inscription->status == 'Pendiente') selected @endif >{{ __('Pendiente') }}</option>
                                                    <option value="Procesando" @if ($inscription->status == 'Procesando') selected @endif>{{ __('Procesando') }}</option>
                                                    <option value="Pagado" @if ($inscription->status == 'Pagado') selected @endif>{{ __('Pagado') }}</option>
                                                    <option value="Rechazado" @if ($inscription->status == 'Rechazado') selected @endif>{{ __('Rechazado') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="note" id="note" placeholder="Nota...">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-secondary">{{ __('Actualizar') }}</button>
                                            </div>
                                        </form>
                                    </div>

                                @endif

                                <div class="card p-2">
                                    <ul class="mb-0">
                                        @foreach ($statusnotes as $item)
                                            <li>
                                                <b class="text-info">{!! $item->note !!}</b> ({{ $item->created_at }})<br>
                                                <small>{{ $item->action }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                            </div>



                            <div class="col-md-5 text-end align-self-end">
                                
                                @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))

                                    @if($inscription->status == 'Pagado' && $inscription->status_compr == 'Ninguna' )
                                        <div id="actionbtncompr">
                                            <a href="javascript:;" class="btn btn-primary mb-1 btnrequestcompr" data-inscription="{{ $inscription->id }}" >{{__("Emitir comprobante")}}</a>
                                        </div>
                                    @endif

                                    @if($inscription->status_compr == 'Pendiente' || $inscription->status_compr == 'Procesando')
                                        <span class="badge badge-light-warning">Generando Comprobante...</span>
                                    @endif
                                
                                @endif

                                @if($inscription->status_compr == 'Informado')
                                    <span class="badge badge-light-success">Comprobante Emitido: {{ $inscription->num_compr }}</span><br>
                                    @if($inscription->compr_pdf == 'T')
                                        <a href="{{ asset('storage/uploads/comprobantes_file').'/'.$inscription->num_compr.'.pdf'}}" target="_blank" class="text-info">{{__("PDF")}}</a> 
                                    @endif

                                    @if($inscription->compr_xml == 'T')
                                        | <a href="{{ asset('storage/uploads/comprobantes_file').'/'.$inscription->num_compr.'.zip'}}" target="_blank" class="text-info">{{__("XML")}}</a> 
                                    @endif

                                    @if($inscription->compr_cdr == 'T')
                                        | <a href="{{ asset('storage/uploads/comprobantes_file').'/R'.$inscription->num_compr.'.zip'}}" target="_blank" class="text-info">{{__("CDR")}}</a>
                                    @endif
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection