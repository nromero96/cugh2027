@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 mb-2 col-12">
                                <h4>
                                    Complete your personal details....
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <form class="row g-3" action="{{ route('inscriptions.storemyinscription') }}" method="POST" id="formInscription" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-4">
                                <label for="salutation" class="form-label fw-bold mb-0">Salutation <span class="text-danger">*</span></label>
                                <select name="salutation" id="salutation" class="form-control" required>
                                    <option value="" disabled selected>Select...</option>
                                    <option value="Mr." {{ old('salutation', $user->salutation) == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                    <option value="Mrs." {{ old('salutation', $user->salutation) == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                    <option value="Ms." {{ old('salutation', $user->salutation) == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                    <option value="Dr." {{ old('salutation', $user->salutation) == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                    <option value="Prof." {{ old('salutation', $user->salutation) == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                                </select>

                                {!!$errors->first("salutation", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-8"></div>

                            <div class="col-md-4">
                                <label for="inputName" class="form-label fw-bold mb-0">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control convert_mayus" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                                {!!$errors->first("name", "<span class='text-danger'>:message</span>")!!}
                            </div>
                            <div class="col-md-4">
                                <label for="inputLastName" class="form-label fw-bold mb-0">Middle Name</label>
                                <input type="text" class="form-control convert_mayus" name="lastname" id="lastname" value="{{ old('lastname', $user->lastname) }}">
                                {!!$errors->first("lastname", "<span class='text-danger'>:message</span>")!!}
                            </div>
                            <div class="col-md-4">
                                <label for="inputSecondLastName" class="form-label fw-bold mb-0">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control convert_mayus" name="second_lastname" id="second_lastname" value="{{ old('second_lastname', $user->second_lastname) }}" required>
                                {!!$errors->first("second_lastname", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            {{-- degrees --}}
                            <div class="col-md-4">
                                <label for="inputDegrees" class="form-label fw-bold mb-0">Degrees <span class="text-danger">*</span></label>
                                <select name="degrees" id="inputDegrees" class="form-select" required>
                                    <option value="" {{ old('degree', $user->degree) == '' ? 'selected' : '' }}>Select...</option>
                                    <option value="Graduate" {{ old('degree', $user->degrees) == 'Graduate' ? 'selected' : '' }}>Graduate</option>
                                    <option value="Master" {{ old('degree', $user->degrees) == 'Master' ? 'selected' : '' }}>Master</option>
                                    <option value="PhD" {{ old('degree', $user->degrees) == 'PhD' ? 'selected' : '' }}>PhD</option>
                                    <option value="Other" {{ old('degree', $user->degrees) == 'Other' ? 'selected' : '' }}>Other (Please specify)</option>
                                </select>
                                {!!$errors->first("degrees", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4 @if(old('degrees', $user->degrees) == 'Other') d-block @else d-none @endif" id="other_degrees_div">
                                <label for="other_degrees" class="form-label fw-bold mb-0">Other Degree <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="other_degrees" id="other_degrees" value="{{ old('other_degrees', $user->other_degrees) }}">
                                {!!$errors->first("other_degrees", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            {{-- is_cugh_member --}}
                            <div class="col-md-4">
                                <label for="inputCUGHMember" class="form-label fw-bold mb-0">CUGH Member <span class="text-danger">*</span></label>
                                {{-- radio options --}}
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_cugh_member" id="cugh_member_no" value="0" {{ old('is_cugh_member', $user->is_cugh_member) == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cugh_member_no">No</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_cugh_member" id="cugh_member_yes" value="1" {{ old('is_cugh_member', $user->is_cugh_member) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cugh_member_yes">Yes</label>
                                    </div>
                                </div>

                                {!!$errors->first("is_cugh_member", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            
                            <div class="col-md-4 @if(old('is_cugh_member', $user->is_cugh_member) == 1) d-block @else d-none @endif" id="cugh_member_institution_div">
                                <label for="inputCUGHMemberInstitution" class="form-label fw-bold mb-0">CUGH Member Institution <span class="text-danger">*</span></label>
                                <select name="cugh_member_institution" id="cugh_member_institution" class="form-select">
                                    <option value="" {{ old('cugh_member_institution', $user->cugh_member_institution) == '' ? 'selected' : '' }}>Select...</option>
                                    <option value="ABH Partners" {{ old('cugh_member_institution', $user->cugh_member_institution) == 'ABH Partners' ? 'selected' : '' }}>ABH Partners</option>
                                    <option value="Academy of Health Sciences" {{ old('cugh_member_institution', $user->cugh_member_institution) == 'Academy of Health Sciences' ? 'selected' : '' }}>Academy of Health Sciences</option>
                                </select>
                                {!!$errors->first("cugh_member_institution", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            {{-- job_title --}}
                            <div class="col-md-4">
                                <label for="inputJobTitle" class="form-label fw-bold mb-0">Job Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control convert_mayus" name="job_title" id="job_title" value="{{ old('job_title', $user->job_title) }}" required>
                                {!!$errors->first("job_title", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputDocumentType" class="form-label fw-bold mb-0">Document Type <span class="text-danger">*</span></label>
                                <select name="document_type" class="form-select" id="inputDocumentType" required>
                                    <option value="" {{ old('document_type', $user->document_type) == '' ? 'selected' : '' }}>Select...</option>
                                    <option value="DNI" {{ old('document_type', $user->document_type) == 'DNI' ? 'selected' : '' }}>DNI (for Peruvian citizens only)</option>
                                    <option value="Passport" {{ old('document_type', $user->document_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
                                </select>
                                {!!$errors->first("document_type", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputDocumentNumber" class="form-label fw-bold mb-0">Document Number <span class="text-danger">*</span></label>
                                <input type="text" name="document_number" class="form-control no-spaces" id="inputDocumentNumber" value="{{ old('document_number', $user->document_number) }}" required>
                                {!!$errors->first("document_number", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputNationality" class="form-label fw-bold mb-0">Nationality  <span class="text-danger">*</span></label>
                                <select name="nationality" class="form-select" id="inputNationality" required>
                                    <option value="" disabled selected>Select...</option>
                                    @foreach ($countries as $nationality)
                                        <option value="{{$nationality->id}}" @if ($user->nationality == $nationality->id) selected="selected" @endif >{{$nationality->name}}</option>
                                    @endforeach
                                </select>
                                {!!$errors->first("nationality", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputGender" class="form-label fw-bold mb-0">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" id="inputGender" required>
                                    <option value="">Select...</option>
                                    <option value="Male" @if ($user->gender == 'Male') selected="selected" @endif>Male</option>
                                    <option value="Female" @if ($user->gender == 'Female') selected="selected" @endif>Female</option>
                                </select>
                                {!!$errors->first("gender", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputOccupation" class="form-label fw-bold mb-0">Occupation <span class="text-danger">*</span></label>
                                <select name="occupation" class="form-select" id="inputOccupation" required>
                                    <option value="">Select...</option>
                                    <option value="Business" @if ($user->occupation == 'Business') selected="selected" @endif>Business</option>
                                    <option value="Legal" @if ($user->occupation == 'Legal') selected="selected" @endif>Legal</option>
                                    <option value="Education" @if ($user->occupation == 'Education') selected="selected" @endif>Education</option>
                                    <option value="Health Care" @if ($user->occupation == 'Health Care') selected="selected" @endif>Health Care</option>
                                    <option value="Media" @if ($user->occupation == 'Media') selected="selected" @endif>Media</option>
                                    <option value="Science and Technology" @if ($user->occupation == 'Science and Technology') selected="selected" @endif>Science and Technology</option>
                                    <option value="Other" @if ($user->occupation == 'Other') selected="selected" @endif>Other</option>
                                </select>
                                {!!$errors->first("occupation", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4 @if ($user->occupation != 'Other') d-none @endif " id="occupation_other">
                                <label for="inputOtherOccupation" class="form-label fw-bold mb-0">Other Occupation <span class="text-danger">*</span></label>
                                <input type="text" name="occupation_other" class="form-control" id="inputOtherOccupation" value="{{$user->occupation_other}}">
                                {!!$errors->first("occupation_other", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-12">
                                    <hr class="mt-1 mb-1">
                                    <div class="row">
                                        <div class="col-md-4 mt-3">
                                            <label for="inputWorkplace" class="form-label fw-bold mb-0">Workplace <span class="text-danger">*</span></label>
                                            <input type="text" name="workplace" class="form-control" id="inputWorkplace" value="{{ old('workplace', $user->workplace) }}" required>
                                            {!!$errors->first("workplace", "<span class='text-danger'>:message</span>")!!}
                                        </div>

                                        <div class="col-md-8 mt-3">
                                            <label for="inputAddress" class="form-label fw-bold mb-0">Work Address <span class="text-danger">*</span></label>
                                            <input type="text" name="address" class="form-control" id="inputAddress" value="{{ old('address', $user->address) }}" maxlength="50" autocomplete="new-work-address" required>
                                            {!!$errors->first("address", "<span class='text-danger'>:message</span>")!!}
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label for="inputCity" class="form-label fw-bold mb-0">City <span class="text-danger">*</span></label>
                                            <input type="text" name="city" class="form-control" id="inputCity" value="{{ old('city', $user->city) }}" required>
                                            {!!$errors->first("city", "<span class='text-danger'>:message</span>")!!}
                                        </div>


                                        <div class="col-md-4 mt-3">
                                            <label for="inputState" class="form-label fw-bold mb-0">State <span class="text-danger">*</span></label>
                                            <input type="text" name="state" class="form-control" id="inputState" value="{{ old('state', $user->state) }}" required>
                                            {!!$errors->first("state", "<span class='text-danger'>:message</span>")!!}
                                        </div>
                                        

                                        <div class="col-md-4 mt-3">
                                            <label for="inputCountry" class="form-label fw-bold mb-0">Country  <span class="text-danger">*</span></label>
                                            <select name="country" class="form-select" id="inputCountry" required>
                                                <option value="" disabled selected>Select...</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{$country->id}}" @if ($user->country == $country->id) selected="selected" @endif >{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                            {!!$errors->first("country", "<span class='text-danger'>:message</span>")!!}
                                        </div>

                                    </div>
                                    <hr class="mb-1">
                            </div>

                            <div class="col-md-4">
                                <label for="inputWorkPhoneNumber" class="form-label fw-bold mb-0">Work Phone</label>
                                <div class="d-flex">
                                    <div class="w-25">
                                        <select name="work_phone_code" class="form-select rounded-0 rounded-start" id="inputPhoneCode">
                                            <option value="" disabled selected>_ _</option>
                                            @foreach ($countries as $country)
                                                <option value="{{$country->phone}}" {{ old('work_phone_code', $user->work_phone_code) == $country->phone ? 'selected' : '' }} >+{{$country->phone}} ({{$country->name}})</option>
                                            @endforeach
                                        </select>
                                        <small>Country</small>
                                    </div>
                                    <div class="w-25">
                                        <input type="text" name="work_phone_code_city" class="form-control no-spaces rounded-0 inputNumber" id="inputWorkPhoneCodeCity" placeholder="_ _" maxlength="5" value="{{ old('work_phone_code_city', $user->work_phone_code_city) }}">
                                        <small>Area code</small>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" name="work_phone_number" class="form-control no-spaces rounded-0 rounded-end inputNumber" id="inputWorkPhoneNumber" placeholder="_ _ _ _ _ _ _ _" maxlength="12" value="{{ old('work_phone_number', $user->work_phone_number) }}">
                                        <small>Number</small>
                                    </div>
                                </div>
                                {!!$errors->first("work_phone_code", "<span class='text-danger'>:message</span>")!!}
                                {!!$errors->first("work_phone_code_city", "<span class='text-danger'>:message</span>")!!}
                                {!!$errors->first("work_phone_number", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputPhoneNumber" class="form-label fw-bold mb-0">Cell Phone <span class="text-danger">*</span></label>
                                <div class="d-flex">
                                    <div class="w-25">
                                        <select name="phone_code" class="form-select rounded-0 rounded-start" id="inputPhoneCode" required>
                                            <option value="" disabled selected>_ _</option>
                                            @foreach ($countries as $country)
                                                <option value="{{$country->phone}}" {{ old('phone_code', $user->phone_code) == $country->phone ? 'selected' : '' }}>+{{$country->phone}} ({{$country->name}})</option>
                                            @endforeach
                                        </select>
                                        <small>Country</small>
                                    </div>
                                    <div class="w-75">
                                        <input type="text" name="phone_number" class="form-control no-spaces rounded-0 rounded-end inputNumber" id="inputPhoneNumber" placeholder="_ _ _ _ _ _ _ _" maxlength="12" value="{{ old('phone_number', $user->phone_number) }}" required>
                                        <small>Number</small>
                                    </div>
                                </div>
                                {!!$errors->first("phone_code", "<span class='text-danger'>:message</span>")!!}
                                {!!$errors->first("phone_number", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputPhoneNumber" class="form-label fw-bold mb-0">WhatsApp</label>
                                <div class="d-flex">
                                    <div class="w-25">
                                        <select name="whatsapp_code" class="form-select rounded-0 rounded-start" id="inputPhoneCode">
                                            <option value="" disabled selected>_ _</option>
                                            @foreach ($countries as $country)
                                                <option value="{{$country->phone}}" {{ old('whatsapp_code', $user->whatsapp_code) == $country->phone ? 'selected' : '' }}>+{{$country->phone}} ({{$country->name}})</option>
                                            @endforeach
                                        </select>
                                        <small>Country</small>
                                    </div>
                                    <div class="w-75">
                                        <input type="text" name="whatsapp_number" class="form-control no-spaces rounded-0 rounded-end inputNumber" id="inputPhoneNumber" placeholder="_ _ _ _ _ _ _ _" maxlength="12" value="{{ old('whatsapp_number', $user->whatsapp_number) }}">
                                        <small>Number</small>
                                    </div>
                                </div>
                                {!!$errors->first("whatsapp_code", "<span class='text-danger'>:message</span>")!!}
                                {!!$errors->first("phone_number", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            

                            <div class="col-md-6">
                                <label for="inputEmail" class="form-label fw-bold mb-0">E-mail <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" id="inputEmail" value="{{$user->email}}" readonly>
                                {!!$errors->first("email", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-6">
                                <label for="inputCcEmail" class="form-label fw-bold mb-0">Cc E-mail</label>
                                <input type="email" name="cc_email" class="form-control" id="inputCcEmail" value="{{ old('cc_email', $user->cc_email) }}">
                                {!!$errors->first("cc_email", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-6">
                                <label for="inputSolapin" class="form-label fw-bold mb-0">Conference badge <span class="text-danger">*</span> <small class="fw-normal">(A first and last name)</small></label>
                                <div class="d-flex">
                                    <input type="text" class="form-control convert_mayus" name="solapin_name" id="inputSolapin" value="{{ old('solapin_name', $user->solapin_name) }}" placeholder="First Name" required>
                                    <input type="text" class="form-control convert_mayus" name="solapin_lastname" id="inputSolapin" value="{{ old('solapin_lastname', $user->solapin_lastname) }}" placeholder="Last Name" required>
                                </div>
                                {!!$errors->first("solapin_name", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-12">
                                <hr class="mt-1">
                            </div>

                            <div class="col-md-12">
                                <h5 class="text-center">Category</h5>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col"><b>Category</b></th>
                                                <th scope="col" width="170px"><b>Registration fee</b></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($category_inscriptions as $category)
                                                @php
                                                    if($category->name == 'Student (Member)' || $category->name == 'Student (Non-Member)' || $category->name == 'Scholars' || $category->name == 'Special Code'){
                                                        $infomark = ' <span class="text-danger">*</span>';
                                                    }else{
                                                        $infomark = '';
                                                    }
                                                @endphp

                                                @if ($category->type == 'radio' && $category->status == 'active')
                                                    <tr>
                                                        <td>
                                                            <div class="form-check form-check-primary me-1">
                                                                <input type="{{ $category->type }}" id="category_{{ $category->id }}" name="category_inscription_id" value="{{ $category->id }}" class="form-check-input cursor-pointer" data-catprice="{{ $category->price }}">
                                                                <label class="form-check-label mb-0 ms-1 cursor-pointer" for="category_{{ $category->id }}">{{ $category->name }}{!! $infomark !!}
                                                                <small class="text-muted">{!! $category->description !!}</small>
                                                                </label>
                                                            </div>

                                                            @if ($category->id == '6')
                                                                <div id="dv_specialcode" class="d-sm-inline-block d-none">
                                                                    <div class="input-group mt-1 mb-0">
                                                                        <input type="text" name="specialcode" id="specialcode" class="form-control convert_mayus" placeholder="Enter Code">
                                                                        <button class="btn btn-secondary d-none" type="button" id="clear_specialcode" style="border-radius: 0px 6px 6px 0px;">Clear</button>
                                                                        <button class="btn btn-primary px-2 px-sm-3" type="button" id="validate_specialcode">Validate</button>
                                                                    </div>
                                                                </div>
                                                                <div class="d-inline-block" id="sms_valid_vc">
                                                                    <!-- Mensaje -->
                                                                </div>
                                                                <input type="hidden" name="specialcode_verify" id="specialcode_verify" value="">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <b>US$ <span id="dc_price_{{ $category->id }}">{{ $category->price === '0.00' ? '00' : rtrim(rtrim($category->price, '0'), '.') }}</span></b>
                                                        </td>
                                                    </tr>

                                                @endif
                                            @endforeach
                                            <tr class="table-secondary">
                                                <td><b>TOTAL</b></td>
                                                <td><b>US$ <span id="paymentotal">00</span></b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div id="dv_document_file" class="d-none">
                                    <small class="text-danger"><b>{{__("Note:")}}</b> * You must attach proof of category (Title, Certificate, Professional Card) (.pdf/.jpg)</small>

                                    <label for="document_file" class="form-label mt-2">
                                        <span class="fw-bold">Attach supporting documentation for category:</span> <span class="text-info"> Title, Certificate, Professional License (.pdf/.jpg)</span></label>
                                    <input type="file" name="document_file" id="document_file" class="file-control">
                                </div>

                            </div>

                            <div class="col-md-12" id="dv_invoice">
                                <div class="card px-3 py-3">
                                    <label for="" class="form-label fw-bold">
                                        Billing information
                                    </label>

                                    

                                    <div class="d-none">
                                        <div class="form-check form-check-primary form-check-inline">
                                            <input type="hidden" name="invoice" id="invoice" value="yes">
                                        </div>
                                    </div>

                                
                                    <div class="">
                                        <div class="form-check form-check-primary form-check-inline" id="dv_invoice_type_boleta">
                                            <input class="form-check-input cursor-pointer" type="radio" name="invoice_type" id="invoice_type_boleta" value="Boleta" checked="">
                                            <label class="form-check-label mb-0 cursor-pointer" for="invoice_type_boleta">
                                                Boleta
                                            </label>
                                        </div>
                                        <div class="form-check form-check-primary form-check-inline d-none" id="dv_invoice_type_factura">
                                            <input class="form-check-input cursor-pointer" type="radio" name="invoice_type" id="invoice_type_factura" value="Factura">
                                            <label class="form-check-label mb-0 cursor-pointer" for="invoice_type_factura">
                                                Factura
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Use personal information for billing --}}
                                    <div class="form-check form-check-primary form-check-inline mt-2">
                                        <input class="form-check-input cursor-pointer" type="checkbox" name="billing_same_as_personal" id="billing_same_as_personal" value="yes">
                                        <label class="form-check-label mb-0 cursor-pointer" for="billing_same_as_personal">
                                            Use personal information for billing
                                        </label>
                                    </div>

                                    <div class="row mt-2" id="dv_invoice_info">
                                        <div class="col-md-4">
                                            <label for="invoice_social_reason" class="form-label mb-0">Name/Entity <span class="text-danger">*</span></label>
                                            <input type="text" name="invoice_social_reason" id="invoice_social_reason" class="form-control" placeholder="Name/Entity" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="invoice_ruc" class="form-label mb-0">Tax ID (RUC) <span class="text-danger">*</span></label>
                                            <input type="text" name="invoice_ruc" id="invoice_ruc" class="form-control no-spaces" placeholder="Tax ID (RUC)" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="invoice_address" class="form-label mb-0">Business Address <span class="text-danger">*</span></label>
                                            <input type="text" name="invoice_address" id="invoice_address" class="form-control" placeholder="Business Address" maxlength="50" autocomplete="new-address" required>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12" id="dv_payment">
                                <div class="card px-3 py-3">
                                    <label for="" class="form-label fw-bold text-center">METHOD OF PAYMENT</label>


                                    <!-- RADIO OCULTO: NO PAYMENT -->
                                        <input type="radio"
                                            name="payment_method"
                                            value="none"
                                            id="payment_method_none"
                                            checked
                                            hidden>

                                    <div class="text-center" id="dv_payment_method">
                                        <div class="form-check form-check-primary form-check-inline">
                                            <input class="form-check-input cursor-pointer" type="radio" name="payment_method" value="Bank Transfer/Wire" id="payment_method_transfer">
                                            <label class="form-check-label mb-0 cursor-pointer" for="payment_method_transfer">
                                                Bank Transfer/Wire
                                            </label>
                                        </div>
                                        <div class="form-check form-check-primary form-check-inline">
                                            <input class="form-check-input cursor-pointer" type="radio" name="payment_method" value="Credit/Debit Card" id="payment_method_card">
                                            <label class="form-check-label mb-0 cursor-pointer" for="payment_method_card">
                                                Credit/Debit Card
                                            </label>
                                        </div>
                                    </div>

                                    <div id="dv_nopayment" class="mt-3 d-none">
                                        <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                                            This category has no registration fee.<br>
                                            Your application will be reviewed before being approved.
                                            <br><br>
                                            You will be notified once the review process is completed.
                                        </div>
                                    </div>

                                    <div id="dv_tranfer" class="mt-3 d-none">
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                Beneficiary:  <b>UNIVERSIDAD PERUANA CAYETANO HEREDIA</b><br>
                                                RUC/TAX ID: 20110768151<br>
                                                Checking Account Number: 191-7318074-1-48<br>
                                                CCI (valid in Peru only): 002 191 007318074148 57<br>
                                                Bank Name: BANCO DE CRÉDITO DEL PERU<br>
                                                Swift Code: BCPLPEPL<br>
                                            </div>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <div id="dv_voucher_file" class="mt-2">
                                                    <label for="voucher_file" class="d-block text-center">Upload copy of wire transfer PDF, JPG format. <small id="cprequired" class="text-danger">(required field)</small></label>
                                                    <input type="file" name="voucher_file" id="voucher_file" class="file-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>

                                    <div id="dv_card" class="pt-4 pb-4 d-none">
                                        <p class="text-center">
                                            <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
                                                <img src="{{ asset('assets/img/pago-tarjeta.png') }}" class="img-fluid" alt="Visa" width="100px">
                                                <br>
                                                Now entering University payment environment to finalize registration.
                                            </div>
                                        </p>
                                    </div>

                                </div>
                            </div>
                            
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg" id="btnSubInscription">Register Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
