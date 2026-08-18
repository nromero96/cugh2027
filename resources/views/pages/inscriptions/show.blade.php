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

                                @if(\Auth::user()->hasRole('Administrador'))
                                    {{-- Editar --}}
                                    <a href="{{ route('inscriptions.edit', $inscription->id) }}" class="btn btn-info px-1 py-1" style="margin-top: -6px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg> Editar</a> 
                                @endif

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
                                <label class="form-label fw-bold mb-0">Salutation:</label><br>
                                <span class="bx-text">{{ $user->salutation }}</span>
                            </div>
                            <div class="col-md-8">
                                
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">First Name:</label><br>
                                <span class="bx-text">{{ $user->name }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Middle Name:</label><br>
                                <span class="bx-text">{{ $user->lastname }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Last Name:</label><br>
                                <span class="bx-text">{{ $user->second_lastname }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Degrees:</label><br>
                                <span class="bx-text">{{ $user->degrees }}</span>
                            </div>

                            <div class="col-md-4 @if($user->degrees != 'Other') d-none @else @endif" id="degrees_other">
                                <label class="form-label fw-bold mb-0">Other Degree:</label><br>
                                <span class="bx-text">{{ $user->other_degrees }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">CUGH Member:</label><br>
                                <span class="bx-text">{{ $user->is_cugh_member == 1 ? 'Yes' : 'No' }}</span>
                            </div>

                            <div class="col-md-4 @if($user->is_cugh_member != 1) d-none @else @endif" id="cugh_membership_type">
                                <label class="form-label fw-bold mb-0">CUGH Membership Type:</label><br>
                                <span class="bx-text">{{ $user->cugh_membership_type}}</span>
                            </div>

                            <div class="col-md-4 @if($user->cugh_membership_type != 'Institutional Member') d-none @else @endif" id="cugh_member_institution">
                                <label class="form-label fw-bold mb-0">CUGH Member Institution:</label><br>
                                <span class="bx-text">{{ $user->cugh_member_institution_name }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Job Title:</label><br>
                                <span class="bx-text">{{ $user->job_title }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Document Type:</label><br>
                                <span class="bx-text">{{ $user->document_type }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Document Number:</label><br>
                                <span class="bx-text">{{ $user->document_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Nationality:</label><br>
                                <span class="bx-text">{{ $user->user_nationality }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Gender:</label><br>
                                <span class="bx-text">{{ $user->gender }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Occupation:</label><br>
                                <span class="bx-text">{{ $user->occupation }}</span>
                            </div>

                            <div class="col-md-4 @if($user->occupation != 'Other') d-none @else @endif" id="occupation_other">
                                <label class="form-label fw-bold mb-0">Other Occupation:</label><br>
                                <span class="bx-text">{{ $user->occupation_other }}</span>
                            </div>

                            <div class="col-md-12">
                                    <hr class="mt-1 mb-0">
                                    <div class="row">
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">Workplace Name:</label><br>
                                            <span class="bx-text">{{ $user->workplace }}</span>
                                        </div>
                                        <div class="col-md-8 mt-3">
                                            <label class="form-label fw-bold mb-0">Workplace Postal Address:</label><br>
                                            <span class="bx-text">{{ $user->address }}</span>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">City:</label><br>
                                            <span class="bx-text">{{ $user->city }}</span>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">State:</label><br>
                                            <span class="bx-text">{{ $user->state }}</span>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">Country:</label><br>
                                            <span class="bx-text">{{ $user->user_country }}</span>
                                        </div>
                                    </div>
                                    <hr class="mt-3 mb-1">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Work Phone:</label><br>
                                <span class="bx-text">{{ $user->work_phone_code.' '.$user->work_phone_code_city.' '.$user->work_phone_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Cell Phone:</label><br>
                                <span class="bx-text">{{ $user->phone_code.' '.$user->phone_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">WhatsApp:</label><br>
                                <span class="bx-text">{{ $user->whatsapp_code.' '.$user->whatsapp_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">E-mail:</label><br>
                                <span class="bx-text">{{ $user->email }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Cc E-mail:</label><br>
                                <span class="bx-text">{{ $user->cc_email }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Conference badge:</label><br>
                                <span class="bx-text">
                                    {{ $user->solapin_name }} | {{ $user->solapin_lastname }}
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

                                    @php 
                                        if($inscription->invoice_type == 'Factura'){
                                            $txt_invoice_social_reason = 'Entity Name';
                                            $txt_invoice_address = 'Business Address';
                                        }else{
                                            $txt_invoice_social_reason = 'Full Name or entity';
                                            $txt_invoice_address = 'Postal Address';
                                        }
                                    @endphp

                                    <div class="row mt-1" id="dv_invoice_info">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold mb-0">{{ $txt_invoice_social_reason }}:</label><br>
                                            <span class="bx-text">{{ $inscription->invoice_social_reason }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold mb-0">Document Type and Number:</label><br>
                                            <span class="bx-text">{{ $inscription->invoice_type_document }} - {{ $inscription->invoice_ruc }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold mb-0">{{ $txt_invoice_address }}:</label><br>
                                            <span class="bx-text">{{ $inscription->invoice_address }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @endif

                            <div class="col-md-12">
                                <div class="card px-3 py-3">
                                    <label for="" class="form-label fw-bold mb-0">Payment Method:</label>
                                    @php 
                                        if($inscription->payment_method == 'none'){
                                            $payment_method = 'No payment required';
                                        }else{
                                            $payment_method = $inscription->payment_method;
                                        }
                                    @endphp

                                    @if($inscription->payment_method != null)
                                    <div class="">
                                        <span class="bx-text">{{ $payment_method }}</span>
                                    </div>
                                    @endif

                                    @if ($inscription->payment_method == 'Bank Transfer/Wire')
                                        <div class="row mt-1">
                                            <div class="col-md-12">
                                                <div class="mt-1">
                                                    @if($inscription->voucher_file != null)
                                                    <a href="{{ asset('storage/uploads/voucher_file').'/'.$inscription->voucher_file}}" class="badge badge-light-primary text-start me-2 bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-bs-original-title="Descargar" target="_blank">
                                                        {{ $inscription->voucher_file }}
                                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7 10 5 5 5-5"></path><path d="M12 15V3"></path></svg>
                                                    </a>
                                                    @else
                                                        <span class="bx-text">No voucher uploaded</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($inscription->payment_method == 'Credit/Debit Card' && $inscription->status == 'Pending')
                                        <div class="row mt-1">
                                            <div class="col-12">
                                                <label class="form-label fw-bold mb-0">Pending Payment:</label><br>
                                                <a href="{{ url(config('services.upch.url_send_data') . '/' . $inscription->token) }}" class="btn btn-primary mt-2">Go to Pay</a>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($inscription->payment_method == 'Credit/Debit Card' && $paymentcards->count() > 0)
                                        @foreach ($paymentcards as $paymentcard)
                                            <div class="card px-3 py-3 mt-3" @if($paymentcard->status_payment == 'AUTORIZADO') style="background-color: #00ab5545;" @else style="background-color: #cc1f2f14;" @endif>
                                                <div class="row mt-1">
                                                    <div class="col-3">
                                                        <label class="form-label fw-bold mb-0"># Transaction number:</label><br>
                                                        <span class="bx-text">{{ $paymentcard->purchasenumber }}</span>
                                                    </div>
                                                    <div class="col-3">
                                                        <label class="form-label fw-bold mb-0">Card #:</label><br>
                                                        <span class="bx-text">{{ $paymentcard->card_number }}</span>
                                                    </div>
                                                    <div class="col-2">
                                                        <label class="form-label fw-bold mb-0">Amount:</label><br>
                                                        <span class="bx-text">{{ $paymentcard->amount.' '.$paymentcard->currency }}</span>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label fw-bold mb-0">Transaction Date:</label><br>
                                                        <span class="bx-text">{{$paymentcard->transaction_date}}</span>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <span class="bx-text">{{ $paymentcard->action_description }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if($inscription->payment_method == null)
                                        <div class="row mt-1">
                                            <div class="col-12">
                                                <span class="bx-text">No payment method selected</span>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>


                            <div class="col-md-7">

                                @if ($inscription->status != 'Confirmed' && (\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria')))
                                    <div class="card px-3 py-3 bg-primary mb-2 actionstatus">
                                        <label class="form-label mb-1 text-white"><span class="fw-bold">{{ __('Estado de la inscripción') }}</span>: <span>({{ $inscription->status }})</span></label>
                                        <form class="row" action="{{ route('inscriptions.updatestatus', ['id' => $inscription->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-4">
                                                <select name="action" id="action" class="form-control">
                                                    <option value="Draft" @if ($inscription->status == 'Draft') selected @endif >{{ __('Draft') }}</option>
                                                    <option value="Pending" @if ($inscription->status == 'Pending') selected @endif >{{ __('Pending') }}</option>
                                                    <option value="Processing" @if ($inscription->status == 'Processing') selected @endif>{{ __('Processing') }}</option>
                                                    <option value="Paid" @if ($inscription->status == 'Paid') selected @endif>{{ __('Paid') }}</option>
                                                    <option value="Refused" @if ($inscription->status == 'Refused') selected @endif>{{ __('Refused') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="note" id="note" placeholder="Note...">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-secondary">{{ __('Update') }}</button>
                                            </div>
                                        </form>
                                    </div>

                                    @if($inscription->status == 'Paid')
                                    <form class="row confirm-form" action="{{ route('inscriptions.updatestatus', ['id' => $inscription->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-md-12">
                                            <input type="hidden" name="note" value="Confirmed">
                                            <input type="hidden" name="action" value="Confirmed">
                                            <button type="submit" class="btn btn-success w-100 mb-2 submit-btn" value="Confirmed">{{ __('Confirmed') }}</button>
                                        </div>
                                    </form>
                                    @endif

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

                            <div class="col-md-5">
                                <div class="card px-2 py-2 @if($inscription->compr_pdf) @else d-none @endif" id="info_invoice">
                                    <h6>Invoice</h6>
                                    <div class="d-flex justify-content-center">
                                        <a href="#" class="me-1 fw-bold">{{ $inscription->compr_pdf }}</a>
                                        <a href="{{ asset('storage/uploads/invoices/'.$inscription->compr_pdf) }}" target="_blank" class="btn btn-light-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>    
                                        </a>
                                        @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                                            <form action="{{ route('inscriptions.deleteinvoice', ['id' => $inscription->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-light-danger ms-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </div>
                                
                                @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                                <div class="card px-2 py-2 @if($inscription->compr_pdf) d-none @endif" id="form_upload_invoice">
                                    <h6>Upload Invoice</h6>
                                    <form action="{{ route('inscriptions.uploadinvoice', $inscription->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="file" class="form-control" name="compr_pdf" id="compr_pdf" accept="application/pdf" required>
                                        <button type="submit" class="btn btn-primary w-100 mt-2">Upload Invoice</button>
                                        @error('compr_pdf')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </form>
                                </div>
                                @endif


                                
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="card">
                                    <div class="card-body disabled-style">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="sector" class="form-label text-muted mb-0 mt-2">SECTOR <span class="text-danger">*</span> <small>(Check all that apply)</small></label><br>
                                                <div class="row">
                                                    @php
                                                        $selectedSectors = old('sector', $user->sector ?? []);
                                                    @endphp
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Academic" {{ in_array('Academic', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Academic</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Funding Organization" {{ in_array('Funding Organization', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Funding Organization</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Government" {{ in_array('Government', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Government</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Intergovernmental organization" {{ in_array('Intergovernmental organization', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Intergovernmental organization</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Non-profit/NGO/Civil Society Organization" {{ in_array('Non-profit/NGO/Civil Society Organization', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Non-profit/NGO/Civil Society Organization</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Private Sector" {{ in_array('Private Sector', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Private Sector</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Research Institute" {{ in_array('Research Institute', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Research Institute</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Think Tank" {{ in_array('Think Tank', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Think Tank</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Other" {{ in_array('Other', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_sector" class="form-control mb-2" id="other_sector" value="{{ old('other_sector', $user->other_sector) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("sector", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="area_of_work" class="form-label text-muted mb-0 mt-2">AREA(S) OF WORK <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                
                                                @php
                                                    $selectedAreaofworks = old('area_of_work', $user->area_of_work ?? []);
                                                @endphp
                                                
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Academic Administration" {{ in_array('Academic Administration', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Academic Administration</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Faculty" {{ in_array('Faculty', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Faculty</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Student Undergraduate" {{ in_array('Student Undergraduate', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Student Undergraduate</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Postgraduate Student" {{ in_array('Postgraduate Student', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Postgraduate Student</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Advocacy" {{ in_array('Advocacy', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Advocacy</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Research" {{ in_array('Research', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Research</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Education" {{ in_array('Education', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Education</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Funding" {{ in_array('Funding', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Funding</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Implementation" {{ in_array('Implementation', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Implementation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Politics/Policy making" {{ in_array('Politics/Policy making', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Politics/Policy making</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Other" {{ in_array('Other', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_area_of_work" class="form-control mb-2" id="other_area_of_work" value="{{ old('other_area_of_work', $user->other_area_of_work) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("area_of_work", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="how_did_you_hear_about" class="form-label text-muted mb-0 mt-2">HOW DID YOU HEAR ABOUT THE CUGH CONFERENCE <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                @php
                                                    $selectedHowdidyouhearabout = old('how_did_you_hear_about', $user->how_did_you_hear_about ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="My institution is a member" {{ in_array('My institution is a member', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">My institution is a member</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Promotional emails from CUGH" {{ in_array('Promotional emails from CUGH', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Promotional emails from CUGH</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="CUGH newsletters" {{ in_array('CUGH newsletters', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">CUGH newsletters</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Saw a flyer/information via non-CUGH site" {{ in_array('Saw a flyer/information via non-CUGH site', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Saw a flyer/information via non-CUGH site</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="From social media (Facebook, Twitter, etc.)" {{ in_array('From social media (Facebook, Twitter, etc.)', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">From social media (Facebook, Twitter, etc.)</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Through a colleague/friend" {{ in_array('Through a colleague/friend', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Through a colleague/friend</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Other" {{ in_array('Other', $selectedHowdidyouhearabout) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" class="form-control mb-2" id="other_how_did_you_hear_about" name="other_how_did_you_hear_about" value="{{ old('other_how_did_you_hear_about', $user->other_how_did_you_hear_about) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("how_did_you_hear_about", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="why_attending" class="form-label text-muted mb-0 mt-2">WHY ARE YOU ATTENDING THE CONFERENCE? <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                @php
                                                    $selectedWhyattending = old('why_attending', $user->why_attending ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To learn about the latest in global health issues" {{ in_array('To learn about the latest in global health issues', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To learn about the latest in global health issues</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To network" {{ in_array('To network', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To network</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To find funding" {{ in_array('To find funding', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To find funding</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To identify a new educational/research opportunity" {{ in_array('To identify a new educational/research opportunity', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To identify a new educational/research opportunity</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To find a job" {{ in_array('To find a job', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To find a job</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To interact with speakers/presenters/moderators" {{ in_array('To interact with speakers/presenters/moderators', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To interact with speakers/presenters/moderators</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To present my work to peers, colleagues, topic experts" {{ in_array('To present my work to peers, colleagues, topic experts', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To present my work to peers, colleagues, topic experts</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="Other" {{ in_array('Other', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_why_attending" class="form-control mb-2" id="other_why_attending" value="{{ old('other_why_attending', $user->other_why_attending) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("why_attending", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="ability_to_present_work" class="form-label text-muted mb-0 mt-2">HOW MUCH DOES THE ABILITY TO PRESENT YOUR WORK AFFECT YOUR ABILITY TO ATTEND THE CONFERENCE? <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-check-label d-block">
                                                            <input class="form-check-input" type="radio" name="ability_to_present_work" value="Essential" {{ old('ability_to_present_work', $user->ability_to_present_work) == 'Essential' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Essential</span>
                                                        </label>
                                                        <label class="form-check-label d-block">
                                                            <input class="form-check-input" type="radio" name="ability_to_present_work" value="Desirable but not essential" {{ old('ability_to_present_work', $user->ability_to_present_work) == 'Desirable but not essential' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Desirable but not essential</span>
                                                        </label>
                                                        <label class="form-check-label d-block">
                                                            <input class="form-check-input" type="radio" name="ability_to_present_work" value="No Effect" {{ old('ability_to_present_work', $user->ability_to_present_work) == 'No Effect' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">No Effect</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("ability_to_present_work", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="how_is_your_attendance_funded" class="form-label text-muted mb-0 mt-2">HOW IS YOUR ATTENDANCE FUNDED? <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                @php
                                                    $selectedHowisyourattendancefunded = old('how_is_your_attendance_funded', $user->how_is_your_attendance_funded ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="By myself" {{ in_array('By myself', $selectedHowisyourattendancefunded) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">By myself</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="By my program/school university" {{ in_array('By my program/school university', $selectedHowisyourattendancefunded) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">By my program/school university</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="By my place of employment" {{ in_array('By my place of employment', $selectedHowisyourattendancefunded) ? 'checked' : '' }} disabled>                                                            <span class="form-check-label">By my place of employment</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="Other" {{ in_array('Other', $selectedHowisyourattendancefunded) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" class="form-control mb-2" name="other_how_is_your_attendance_funded" id="other_how_is_your_attendance_funded" value="{{ old('other_how_is_your_attendance_funded', $user->other_how_is_your_attendance_funded) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("how_is_your_attendance_funded", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="your_areas_of_focus_in_global_health" class="form-label text-muted mb-0 mt-2">YOUR AREAS OF FOCUS IN GLOBAL HEALTH <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                @php
                                                    $selectedYourareasglobal = old('your_areas_of_focus_in_global_health', $user->your_areas_of_focus_in_global_health ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Administration" {{ in_array('Administration', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Administration</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Advocacy/Communication" {{ in_array('Advocacy/Communication', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Advocacy/Communication</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Capacity Building" {{ in_array('Capacity Building', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Capacity Building</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Disaster Management" {{ in_array('Disaster Management', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Disaster Management</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Education" {{ in_array('Education', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Education</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Emergency Medicine" {{ in_array('Emergency Medicine', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Emergency Medicine</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Environment/One Health/Planetary Health" {{ in_array('Environment/One Health/Planetary Health', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Environment/One Health/Planetary Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Governance" {{ in_array('Governance', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Governance</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Economics" {{ in_array('Economics', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Economics</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Policy" {{ in_array('Policy', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Policy</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Implementation" {{ in_array('Implementation', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Implementation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Infectious Diseases/Pandemic prevention and response" {{ in_array('Infectious Diseases/Pandemic prevention and response', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Infectious Diseases/Pandemic prevention and response</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Mental Health" {{ in_array('Mental Health', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Mental Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="NCDS" {{ in_array('NCDS', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">NCDS</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Nutrition/Food Security" {{ in_array('Nutrition/Food Security', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Nutrition/Food Security</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Oral Health" {{ in_array('Oral Health', $selectedYourareasglobal) ? 'checked' : '' }} disabled>                                                            <span class="form-check-label">Oral Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Pediatrics" {{ in_array('Pediatrics', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Pediatrics</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Politics/Political Science" {{ in_array('Politics/Political Science', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Politics/Political Science</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Public Health" {{ in_array('Public Health', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Public Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Research" {{ in_array('Research', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Research</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Social Sciences" {{ in_array('Social Sciences', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Social Sciences</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Student Services/International Education" {{ in_array('Student Services/International Education', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Student Services/International Education</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Surgery/Trauma" {{ in_array('Surgery/Trauma', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Surgery/Trauma</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Veterinary Sciences" {{ in_array('Veterinary Sciences', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Veterinary Sciences</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Water/Sanitation" {{ in_array('Water/Sanitation', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Water/Sanitation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Women's Health" {{ in_array("Women's Health", $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Women's Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Other" {{ in_array('Other', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_your_areas_of_focus_in_global_health" id="other_your_areas_of_focus_in_global_health" class="form-control mb-2" value="{{ old('other_your_areas_of_focus_in_global_health', $user->other_your_areas_of_focus_in_global_health) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("your_areas_of_focus_in_global_health", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="obstacles_to_attending_cughs_conferences" class="form-label text-muted mb-0 mt-2">OBSTACLES TO ATTENDING CUGH'S CONFERENCES <span class="text-danger">*</span></label>
                                                @php
                                                    $selectedObstaclestoattendingcughsconferences = old('obstacles_to_attending_cughs_conferences', $user->obstacles_to_attending_cughs_conferences ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="obstacles_to_attending_cughs_conferences[]" value="Financial" {{ in_array('Financial', $selectedObstaclestoattendingcughsconferences) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Financial</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="obstacles_to_attending_cughs_conferences[]" value="Visas & other immigration factors" {{ in_array('Visas & other immigration factors', $selectedObstaclestoattendingcughsconferences) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Visas & other immigration factors</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="obstacles_to_attending_cughs_conferences[]" value="Other" {{ in_array('Other', $selectedObstaclestoattendingcughsconferences) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_obstacles_to_attending_cughs_conferences" id="other_obstacles_to_attending_cughs_conferences" class="form-control mb-2" value="{{ old('other_obstacles_to_attending_cughs_conferences', $user->other_obstacles_to_attending_cughs_conferences) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("obstacles_to_attending_cughs_conferences", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="receive_news_and_updates" class="form-label text-muted mb-0 mt-2">I WANT TO RECEIVE NEWS AND UPDATES ABOUT FUTURE CUGH ACTIVITIES AND EVENTS <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="receive_news_and_updates" value="Yes" {{ old('receive_news_and_updates', $user->receive_news_and_updates) == 'Yes' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">YES, I wish to receive CUGH news</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="receive_news_and_updates" value="No" {{ old('receive_news_and_updates', $user->receive_news_and_updates) == 'No' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">NO, I do not wish to receive CUGH news</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("receive_news_and_updates", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                {{-- CONTACT INFO --}}
                                                <label for="contact_info" class="form-label text-muted mb-0 mt-2">CONTACT INFORMATION <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="contact_info" value="I agree that my contact information can be shared with other attendees (Conference App)" {{ old('contact_info', $user->contact_info) == 'I agree that my contact information can be shared with other attendees (Conference App)' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">I agree that my contact information can be shared with other attendees (Conference App).</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="contact_info" value="I do not wish my contact information to be shared with other attendees" {{ old('contact_info', $user->contact_info) == 'I do not wish my contact information to be shared with other attendees' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">I do not wish my contact information to be shared with other attendees</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("contact_info", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                {{-- ORAL/POSTER ABSTRACT PRESENTER? (NOT APPLICABLE FOR PANEL SPEAKERS) --}}
                                                <label for="oral_poster_abstract_presenter" class="form-label text-muted mb-0 mt-2">ORAL/POSTER ABSTRACT PRESENTER? <span class="text-danger">*</span> <small>(Not Applicable for Panel Speakers)</small></label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="oral_poster_abstract_presenter" value="Yes" {{ old('oral_poster_abstract_presenter', $user->oral_poster_abstract_presenter) == 'Yes' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">YES, I will present a poster or oral abstract presentation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="oral_poster_abstract_presenter" value="No" {{ old('oral_poster_abstract_presenter', $user->oral_poster_abstract_presenter) == 'No' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">NO, I am not presenting a poster or oral abstract presentation</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("oral_poster_abstract_presenter", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                {{-- PANEL PRESENTER/MODERATOR? (NOT APPLICABLE FOR SCIENTIFIC ABSTRACT SUBMITTERS) --}}
                                                <label for="panel_presenter_moderator" class="form-label text-muted mb-0 mt-2">PANEL PRESENTER/MODERATOR? <span class="text-danger">*</span> <small>(Not Applicable for Scientific Abstract Submitters)</small></label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="panel_presenter_moderator" value="Yes" {{ old('panel_presenter_moderator', $user->panel_presenter_moderator) == 'Yes' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">YES, I will be a panel speaker/moderator</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="panel_presenter_moderator" value="No" {{ old('panel_presenter_moderator', $user->panel_presenter_moderator) == 'No' ? 'checked' : '' }} disabled >
                                                            <span class="form-check-label">NO, I am not a panel speaker/moderator</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("panel_presenter_moderator", "<span class='text-danger'>:message</span>")!!}
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.confirm-form').forEach(function(form) {

        form.addEventListener('submit', function(e) {

            // Mostrar alerta de confirmación
            const confirmAction = confirm('Are you sure you want to confirm this inscription?');

            // Si cancela
            if (!confirmAction) {
                e.preventDefault();
                return;
            }

            // Bloquear botón para evitar doble click
            const button = form.querySelector('.submit-btn');

            button.disabled = true;
            button.innerHTML = 'Processing...';

        });

    });

});
</script>

@endsection