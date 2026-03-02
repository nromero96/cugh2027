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
                            <div class="col-8">
                                <h4>
                                    My Registration #{{ $myinscription->id }}
                                </h4>
                            </div>
                            <div class="col-4 text-end">
                                @php 
                                $lastUpdated = $user->updated_at > $myinscription->updated_at 
                                                                ? $user->updated_at 
                                                                : $myinscription->updated_at;
                                @endphp


                                <span class="text-muted d-block mt-3">
                                    Last updated {{ $lastUpdated->format('d M Y - H:i:s') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <form class="row g-3" action="{{ route('inscriptions.storemyinscription') }}" method="POST" id="formInscription" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-4">
                                <label for="salutation" class="form-label text-muted mb-0">Salutation <span class="text-danger">*</span></label>
                                <select name="salutation" id="salutation" class="form-control">
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
                                <label for="inputName" class="form-label text-muted mb-0">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control convert_mayus" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                                {!!$errors->first("name", "<span class='text-danger'>:message</span>")!!}
                            </div>
                            <div class="col-md-4">
                                <label for="inputLastName" class="form-label text-muted mb-0">Middle Name</label>
                                <input type="text" class="form-control convert_mayus" name="lastname" id="lastname" value="{{ old('lastname', $user->lastname) }}">
                                {!!$errors->first("lastname", "<span class='text-danger'>:message</span>")!!}
                            </div>
                            <div class="col-md-4">
                                <label for="inputSecondLastName" class="form-label text-muted mb-0">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control convert_mayus" name="second_lastname" id="second_lastname" value="{{ old('second_lastname', $user->second_lastname) }}" required>
                                {!!$errors->first("second_lastname", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            {{-- degrees --}}
                            <div class="col-md-4">
                                <label for="inputDegrees" class="form-label text-muted mb-0">Degrees <span class="text-danger">*</span></label>
                                <select name="degrees" id="inputDegrees" class="form-select">
                                    <option value="" {{ old('degree', $user->degree) == '' ? 'selected' : '' }}>Select...</option>
                                    <option value="Graduate" {{ old('degree', $user->degrees) == 'Graduate' ? 'selected' : '' }}>Graduate</option>
                                    <option value="Master" {{ old('degree', $user->degrees) == 'Master' ? 'selected' : '' }}>Master</option>
                                    <option value="PhD" {{ old('degree', $user->degrees) == 'PhD' ? 'selected' : '' }}>PhD</option>
                                    <option value="Other" {{ old('degree', $user->degrees) == 'Other' ? 'selected' : '' }}>Other (Please specify)</option>
                                </select>
                                {!!$errors->first("degrees", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4 @if(old('degrees', $user->degrees) == 'Other') d-block @else d-none @endif" id="other_degrees_div">
                                <label for="other_degrees" class="form-label text-muted mb-0">Other Degree <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="other_degrees" id="other_degrees" value="{{ old('other_degrees', $user->other_degrees) }}">
                                {!!$errors->first("other_degrees", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            {{-- is_cugh_member --}}
                            <div class="col-md-4">
                                <label for="inputCUGHMember" class="form-label text-muted mb-0">CUGH Member <span class="text-danger">*</span></label>
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
                                <label for="inputCUGHMemberInstitution" class="form-label text-muted mb-0">CUGH Member Institution <span class="text-danger">*</span></label>
                                <select name="cugh_member_institution" id="cugh_member_institution" class="form-select">
                                    <option value="" {{ old('cugh_member_institution', $user->cugh_member_institution) == '' ? 'selected' : '' }}>Select...</option>
                                    
                                    @foreach($memberinstitutions as $memberinstitution)
                                        <option value="{{ $memberinstitution->id }}" {{ old('cugh_member_institution', $user->cugh_member_institution) == $memberinstitution->id ? 'selected' : '' }}>{{ $memberinstitution->name }}</option>
                                    @endforeach

                                </select>
                                {!!$errors->first("cugh_member_institution", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            {{-- job_title --}}
                            <div class="col-md-4">
                                <label for="inputJobTitle" class="form-label text-muted mb-0">Job Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control convert_mayus" name="job_title" id="job_title" value="{{ old('job_title', $user->job_title) }}">
                                {!!$errors->first("job_title", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputDocumentType" class="form-label text-muted mb-0">Document Type <span class="text-danger">*</span></label>
                                <select name="document_type" class="form-select" id="inputDocumentType" required>
                                    <option value="" {{ old('document_type', $user->document_type) == '' ? 'selected' : '' }}>Select...</option>
                                    <option value="DNI" {{ old('document_type', $user->document_type) == 'DNI' ? 'selected' : '' }}>DNI (for Peruvian citizens only)</option>
                                    <option value="Passport" {{ old('document_type', $user->document_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
                                </select>
                                {!!$errors->first("document_type", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputDocumentNumber" class="form-label text-muted mb-0">Document Number <span class="text-danger">*</span></label>
                                <input type="text" name="document_number" class="form-control no-spaces" id="inputDocumentNumber" value="{{ old('document_number', $user->document_number) }}" required>
                                {!!$errors->first("document_number", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputNationality" class="form-label text-muted mb-0">Nationality  <span class="text-danger">*</span></label>
                                <select name="nationality" class="form-select" id="inputNationality">
                                    <option value="" disabled selected>Select...</option>
                                    @foreach ($countries as $nationality)
                                        <option value="{{$nationality->id}}" @if ($user->nationality == $nationality->id) selected="selected" @endif >{{$nationality->name}}</option>
                                    @endforeach
                                </select>
                                {!!$errors->first("nationality", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputGender" class="form-label text-muted mb-0">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" id="inputGender">
                                    <option value="">Select...</option>
                                    <option value="Male" @if ($user->gender == 'Male') selected="selected" @endif>Male</option>
                                    <option value="Female" @if ($user->gender == 'Female') selected="selected" @endif>Female</option>
                                </select>
                                {!!$errors->first("gender", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputOccupation" class="form-label text-muted mb-0">Occupation <span class="text-danger">*</span></label>
                                <select name="occupation" class="form-select" id="inputOccupation">
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
                                <label for="inputOtherOccupation" class="form-label text-muted mb-0">Other Occupation <span class="text-danger">*</span></label>
                                <input type="text" name="occupation_other" class="form-control" id="inputOtherOccupation" value="{{$user->occupation_other}}">
                                {!!$errors->first("occupation_other", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-12">
                                    <hr class="mt-1 mb-1">
                                    <div class="row">
                                        <div class="col-md-4 mt-3">
                                            <label for="inputWorkplace" class="form-label text-muted mb-0">Workplace <span class="text-danger">*</span></label>
                                            <input type="text" name="workplace" class="form-control" id="inputWorkplace" value="{{ old('workplace', $user->workplace) }}">
                                            {!!$errors->first("workplace", "<span class='text-danger'>:message</span>")!!}
                                        </div>

                                        <div class="col-md-8 mt-3">
                                            <label for="inputAddress" class="form-label text-muted mb-0">Work Address <span class="text-danger">*</span></label>
                                            <input type="text" name="address" class="form-control" id="inputAddress" value="{{ old('address', $user->address) }}" maxlength="50" autocomplete="new-work-address">
                                            {!!$errors->first("address", "<span class='text-danger'>:message</span>")!!}
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label for="inputCity" class="form-label text-muted mb-0">City <span class="text-danger">*</span></label>
                                            <input type="text" name="city" class="form-control" id="inputCity" value="{{ old('city', $user->city) }}">
                                            {!!$errors->first("city", "<span class='text-danger'>:message</span>")!!}
                                        </div>


                                        <div class="col-md-4 mt-3">
                                            <label for="inputState" class="form-label text-muted mb-0">State <span class="text-danger">*</span></label>
                                            <input type="text" name="state" class="form-control" id="inputState" value="{{ old('state', $user->state) }}">
                                            {!!$errors->first("state", "<span class='text-danger'>:message</span>")!!}
                                        </div>
                                        

                                        <div class="col-md-4 mt-3">
                                            <label for="inputCountry" class="form-label text-muted mb-0">Country  <span class="text-danger">*</span></label>
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
                                <label for="inputWorkPhoneNumber" class="form-label text-muted mb-0">Work Phone</label>
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
                                <label for="inputPhoneNumber" class="form-label text-muted mb-0">Cell Phone <span class="text-danger">*</span></label>
                                <div class="d-flex">
                                    <div class="w-25">
                                        <select name="phone_code" class="form-select rounded-0 rounded-start" id="inputPhoneCode">
                                            <option value="" disabled selected>_ _</option>
                                            @foreach ($countries as $country)
                                                <option value="{{$country->phone}}" {{ old('phone_code', $user->phone_code) == $country->phone ? 'selected' : '' }}>+{{$country->phone}} ({{$country->name}})</option>
                                            @endforeach
                                        </select>
                                        <small>Country</small>
                                    </div>
                                    <div class="w-75">
                                        <input type="text" name="phone_number" class="form-control no-spaces rounded-0 rounded-end inputNumber" id="inputPhoneNumber" placeholder="_ _ _ _ _ _ _ _" maxlength="12" value="{{ old('phone_number', $user->phone_number) }}">
                                        <small>Number</small>
                                    </div>
                                </div>
                                {!!$errors->first("phone_code", "<span class='text-danger'>:message</span>")!!}
                                {!!$errors->first("phone_number", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputPhoneNumber" class="form-label text-muted mb-0">WhatsApp</label>
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
                                <label for="inputEmail" class="form-label text-muted mb-0">E-mail <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" id="inputEmail" value="{{$user->email}}" readonly>
                                {!!$errors->first("email", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-6">
                                <label for="inputCcEmail" class="form-label text-muted mb-0">Cc E-mail</label>
                                <input type="email" name="cc_email" class="form-control" id="inputCcEmail" value="{{ old('cc_email', $user->cc_email) }}">
                                {!!$errors->first("cc_email", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-6">
                                <label for="inputSolapin" class="form-label text-muted mb-0">Conference badge <span class="text-danger">*</span> <small class="fw-normal">(A first and last name)</small></label>
                                <div class="d-flex">
                                    <input type="text" class="form-control convert_mayus" name="solapin_name" id="inputSolapin" value="{{ old('solapin_name', $user->solapin_name) }}" placeholder="First Name" >
                                    <input type="text" class="form-control convert_mayus" name="solapin_lastname" id="inputSolapin" value="{{ old('solapin_lastname', $user->solapin_lastname) }}" placeholder="Last Name" >
                                </div>
                                {!!$errors->first("solapin_name", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            

                            <div class="col-md-12 mt-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="sector" class="form-label text-muted mb-0 mt-2">SECTOR <span class="text-danger">*</span> <small>(Check all that apply)</small></label><br>
                                                <div class="row">
                                                    @php
                                                        $selectedSectors = old('sector', $user->sector ?? []);
                                                    @endphp
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Academic" {{ in_array('Academic', $selectedSectors) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Academic</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Funding Organization" {{ in_array('Funding Organization', $selectedSectors) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Funding Organization</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Government" {{ in_array('Government', $selectedSectors) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Government</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Intergovernmental organization" {{ in_array('Intergovernmental organization', $selectedSectors) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Intergovernmental organization</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Non-profit/NGO/Civil Society Organization" {{ in_array('Non-profit/NGO/Civil Society Organization', $selectedSectors) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Non-profit/NGO/Civil Society Organization</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Private Sector" {{ in_array('Private Sector', $selectedSectors) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Private Sector</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Research Institute" {{ in_array('Research Institute', $selectedSectors) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Research Institute</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Think Tank" {{ in_array('Think Tank', $selectedSectors) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Think Tank</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Other" {{ in_array('Other', $selectedSectors) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_sector" class="form-control mb-2" id="other_sector" value="{{ old('other_sector', $user->other_sector) }}" placeholder="Please specify">
                                                    </div>
                                                </div>
                                                {!!$errors->first("sector", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="area_of_work" class="form-label text-muted mb-0 mt-2">AREA(S) OF WORK <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                
                                                @php
                                                    $selectedAreaofworks = old('sector', $user->area_of_work ?? []);
                                                @endphp
                                                
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Academic Administration" {{ in_array('Academic Administration', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Academic Administration</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Faculty" {{ in_array('Faculty', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Faculty</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Student Undergraduate" {{ in_array('Student Undergraduate', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Student Undergraduate</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Postgraduate Student" {{ in_array('Postgraduate Student', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Postgraduate Student</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Advocacy" {{ in_array('Advocacy', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Advocacy</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Research" {{ in_array('Research', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Research</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Education" {{ in_array('Education', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Education</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Funding" {{ in_array('Funding', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Funding</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Implementation" {{ in_array('Implementation', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Implementation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Politics/Policy making" {{ in_array('Politics/Policy making', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Politics/Policy making</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Other" {{ in_array('Other', $selectedAreaofworks) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_area_of_work" class="form-control mb-2" id="other_area_of_work" value="{{ old('other_area_of_work', $user->other_area_of_work) }}" placeholder="Please specify">
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
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="My institution is a member" {{ in_array('My institution is a member', $selectedHowdidyouhearabout) ? 'checked' : '' }}>
                                                            <span class="form-check-label">My institution is a member</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Promotional emails from CUGH" {{ in_array('Promotional emails from CUGH', $selectedHowdidyouhearabout) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Promotional emails from CUGH</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="CUGH newsletters" {{ in_array('CUGH newsletters', $selectedHowdidyouhearabout) ? 'checked' : '' }}>
                                                            <span class="form-check-label">CUGH newsletters</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Saw a flyer/information via non-CUGH site" {{ in_array('Saw a flyer/information via non-CUGH site', $selectedHowdidyouhearabout) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Saw a flyer/information via non-CUGH site</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="From social media (Facebook, Twitter, etc.)" {{ in_array('From social media (Facebook, Twitter, etc.)', $selectedHowdidyouhearabout) ? 'checked' : '' }}>
                                                            <span class="form-check-label">From social media (Facebook, Twitter, etc.)</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Through a colleague/friend" {{ in_array('Through a colleague/friend', $selectedHowdidyouhearabout) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Through a colleague/friend</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Other" {{ in_array('Other', $selectedHowdidyouhearabout) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" class="form-control mb-2" id="other_how_did_you_hear_about" name="other_how_did_you_hear_about" value="{{ old('other_how_did_you_hear_about', $user->other_how_did_you_hear_about) }}" placeholder="Please specify">
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
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To learn about the latest in global health issues" {{ in_array('To learn about the latest in global health issues', $selectedWhyattending) ? 'checked' : '' }}>
                                                            <span class="form-check-label">To learn about the latest in global health issues</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To network" {{ in_array('To network', $selectedWhyattending) ? 'checked' : '' }}>
                                                            <span class="form-check-label">To network</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To find funding" {{ in_array('To find funding', $selectedWhyattending) ? 'checked' : '' }}>
                                                            <span class="form-check-label">To find funding</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To identify a new educational/research opportunity" {{ in_array('To identify a new educational/research opportunity', $selectedWhyattending) ? 'checked' : '' }}>
                                                            <span class="form-check-label">To identify a new educational/research opportunity</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To find a job" {{ in_array('To find a job', $selectedWhyattending) ? 'checked' : '' }}>
                                                            <span class="form-check-label">To find a job</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To interact with speakers/presenters/moderators" {{ in_array('To interact with speakers/presenters/moderators', $selectedWhyattending) ? 'checked' : '' }}>
                                                            <span class="form-check-label">To interact with speakers/presenters/moderators</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To present my work to peers, colleagues, topic experts" {{ in_array('To present my work to peers, colleagues, topic experts', $selectedWhyattending) ? 'checked' : '' }}>
                                                            <span class="form-check-label">To present my work to peers, colleagues, topic experts</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="Other" {{ in_array('Other', $selectedWhyattending) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_why_attending" class="form-control mb-2" id="other_why_attending" value="{{ old('other_why_attending', $user->other_why_attending) }}" placeholder="Please specify">
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
                                                            <input class="form-check-input" type="radio" name="ability_to_present_work" value="Essential" {{ old('ability_to_present_work', $user->ability_to_present_work) == 'Essential' ? 'checked' : '' }}>
                                                            <span class="form-check-label">Essential</span>
                                                        </label>
                                                        <label class="form-check-label d-block">
                                                            <input class="form-check-input" type="radio" name="ability_to_present_work" value="Desirable but not essential" {{ old('ability_to_present_work', $user->ability_to_present_work) == 'Desirable but not essential' ? 'checked' : '' }}>
                                                            <span class="form-check-label">Desirable but not essential</span>
                                                        </label>
                                                        <label class="form-check-label d-block">
                                                            <input class="form-check-input" type="radio" name="ability_to_present_work" value="No Effect" {{ old('ability_to_present_work', $user->ability_to_present_work) == 'No Effect' ? 'checked' : '' }}>
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
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="By myself" {{ in_array('By myself', $selectedHowisyourattendancefunded) ? 'checked' : '' }}>
                                                            <span class="form-check-label">By myself</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="By my program/school university" {{ in_array('By my program/school university', $selectedHowisyourattendancefunded) ? 'checked' : '' }}>
                                                            <span class="form-check-label">By my program/school university</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="By my place of employment" {{ in_array('By my place of employment', $selectedHowisyourattendancefunded) ? 'checked' : '' }}>                                                            <span class="form-check-label">By my place of employment</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="Other" {{ in_array('Other', $selectedHowisyourattendancefunded) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" class="form-control mb-2" name="other_how_is_your_attendance_funded" id="other_how_is_your_attendance_funded" value="{{ old('other_how_is_your_attendance_funded', $user->other_how_is_your_attendance_funded) }}" placeholder="Please specify">
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
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Administration" {{ in_array('Administration', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Administration</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Advocacy/Communication" {{ in_array('Advocacy/Communication', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Advocacy/Communication</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Capacity Building" {{ in_array('Capacity Building', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Capacity Building</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Disaster Management" {{ in_array('Disaster Management', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Disaster Management</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Education" {{ in_array('Education', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Education</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Emergency Medicine" {{ in_array('Emergency Medicine', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Emergency Medicine</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Environment/One Health/Planetary Health" {{ in_array('Environment/One Health/Planetary Health', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Environment/One Health/Planetary Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Governance" {{ in_array('Governance', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Governance</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Economics" {{ in_array('Economics', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Economics</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Policy" {{ in_array('Policy', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Policy</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Implementation" {{ in_array('Implementation', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Implementation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Infectious Diseases/Pandemic prevention and response" {{ in_array('Infectious Diseases/Pandemic prevention and response', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Infectious Diseases/Pandemic prevention and response</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Mental Health" {{ in_array('Mental Health', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Mental Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="NCDS" {{ in_array('NCDS', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">NCDS</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Nutrition/Food Security" {{ in_array('Nutrition/Food Security', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Nutrition/Food Security</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Oral Health" {{ in_array('Oral Health', $selectedYourareasglobal) ? 'checked' : '' }}>                                                            <span class="form-check-label">Oral Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Pediatrics" {{ in_array('Pediatrics', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Pediatrics</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Politics/Political Science" {{ in_array('Politics/Political Science', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Politics/Political Science</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Public Health" {{ in_array('Public Health', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Public Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Research" {{ in_array('Research', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Research</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Social Sciences" {{ in_array('Social Sciences', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Social Sciences</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Student Services/International Education" {{ in_array('Student Services/International Education', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Student Services/International Education</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Surgery/Trauma" {{ in_array('Surgery/Trauma', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Surgery/Trauma</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Veterinary Sciences" {{ in_array('Veterinary Sciences', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Veterinary Sciences</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Water/Sanitation" {{ in_array('Water/Sanitation', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Water/Sanitation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Women's Health" {{ in_array("Women's Health", $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Women's Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Other" {{ in_array('Other', $selectedYourareasglobal) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_your_areas_of_focus_in_global_health" id="other_your_areas_of_focus_in_global_health" class="form-control mb-2" value="{{ old('other_your_areas_of_focus_in_global_health', $user->other_your_areas_of_focus_in_global_health) }}" placeholder="Please specify">
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
                                                            <input class="form-check-input" type="checkbox" name="obstacles_to_attending_cughs_conferences[]" value="Financial" {{ in_array('Financial', $selectedObstaclestoattendingcughsconferences) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Financial</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="obstacles_to_attending_cughs_conferences[]" value="Visas & other immigration factors" {{ in_array('Visas & other immigration factors', $selectedObstaclestoattendingcughsconferences) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Visas & other immigration factors</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="obstacles_to_attending_cughs_conferences[]" value="Other" {{ in_array('Other', $selectedObstaclestoattendingcughsconferences) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_obstacles_to_attending_cughs_conferences" id="other_obstacles_to_attending_cughs_conferences" class="form-control mb-2" value="{{ old('other_obstacles_to_attending_cughs_conferences', $user->other_obstacles_to_attending_cughs_conferences) }}" placeholder="Please specify">
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
                                                            <input class="form-check-input" type="radio" name="receive_news_and_updates" value="Yes" {{ old('receive_news_and_updates', $user->receive_news_and_updates) == 'Yes' ? 'checked' : '' }}>
                                                            <span class="form-check-label">YES, I wish to receive CUGH news</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="receive_news_and_updates" value="No" {{ old('receive_news_and_updates', $user->receive_news_and_updates) == 'No' ? 'checked' : '' }}>
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
                                                            <input class="form-check-input" type="radio" name="contact_info" value="I agree that my contact information can be shared with other attendees (Conference App)" {{ old('contact_info', $user->contact_info) == 'I agree that my contact information can be shared with other attendees (Conference App)' ? 'checked' : '' }}>
                                                            <span class="form-check-label">I agree that my contact information can be shared with other attendees (Conference App).</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="contact_info" value="I do not wish my contact information to be shared with other attendees" {{ old('contact_info', $user->contact_info) == 'I do not wish my contact information to be shared with other attendees' ? 'checked' : '' }}>
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
                                                            <input class="form-check-input" type="radio" name="oral_poster_abstract_presenter" value="Yes" {{ old('oral_poster_abstract_presenter', $user->oral_poster_abstract_presenter) == 'Yes' ? 'checked' : '' }}>
                                                            <span class="form-check-label">YES, I will present a poster or oral abstract presentation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="oral_poster_abstract_presenter" value="No" {{ old('oral_poster_abstract_presenter', $user->oral_poster_abstract_presenter) == 'No' ? 'checked' : '' }}>
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
                                                            <input class="form-check-input" type="radio" name="panel_presenter_moderator" value="Yes" {{ old('panel_presenter_moderator', $user->panel_presenter_moderator) == 'Yes' ? 'checked' : '' }}>
                                                            <span class="form-check-label">YES, I will be a panel speaker/moderator</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="panel_presenter_moderator" value="No" {{ old('panel_presenter_moderator', $user->panel_presenter_moderator) == 'No' ? 'checked' : '' }}>
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
                                                                <input type="{{ $category->type }}" id="category_{{ $category->id }}" name="category_inscription_id" value="{{ $category->id }}" class="form-check-input cursor-pointer" data-catprice="{{ $category->price }}" {{ old('category_inscription_id', $myinscription->category_inscription_id) == $category->id ? 'checked' : '' }}>
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
                                        <span class="fw-bold">Attach supporting documentation for category:</span> <span class="text-info"> Title, Certificate, Professional License (.pdf/.jpg)</span>
                                    </label>

                                    @if (!empty($myinscription->document_file))
                                    <div class="mt-2">
                                        <div class="card border shadow-sm w-100">
                                            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                                                <!-- Nombre + Descargar -->
                                                <a href="{{ asset('storage/uploads/document_file/'.$myinscription->document_file) }}"
                                                target="_blank"
                                                class="text-decoration-none fw-semibold text-primary d-flex align-items-center gap-2">

                                                    <i class="bi bi-file-earmark-arrow-down fs-5"></i>
                                                    {{ $myinscription->document_file }}
                                                </a>

                                                <!-- Botón eliminar -->
                                                <form action="" 
                                                    method="POST"
                                                    onsubmit="return confirm('¿Seguro que deseas eliminar este archivo?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <input type="file" name="document_file" id="document_file" class="file-control d-none">

                                    
                                    
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
                                            <input class="form-check-input cursor-pointer" type="radio" name="invoice_type" id="invoice_type_boleta" value="Boleta" {{ old('invoice_type', $myinscription->invoice_type ?? 'Boleta') == 'Boleta' ? 'checked' : '' }}>
                                            <label class="form-check-label mb-0 cursor-pointer" for="invoice_type_boleta">
                                                Boleta
                                            </label>
                                        </div>
                                        <div class="form-check form-check-primary form-check-inline d-none" id="dv_invoice_type_factura">
                                            <input class="form-check-input cursor-pointer" type="radio" name="invoice_type" id="invoice_type_factura" value="Factura" {{ old('invoice_type', $myinscription->invoice_type ?? 'Factura') == 'Factura' ? 'checked' : '' }}>
                                            <label class="form-check-label mb-0 cursor-pointer" for="invoice_type_factura">
                                                Factura
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row mt-2" id="dv_invoice_info">
                                        <div class="col-md-4">
                                            <label for="invoice_social_reason" class="form-label text-muted mb-0"><span id="lbl_invoice_social_reason">Name/Entity</span> <span class="text-danger">*</span></label>
                                            <input type="text" name="invoice_social_reason" id="invoice_social_reason" class="form-control" placeholder="" value="{{ old('invoice_social_reason', $myinscription->invoice_social_reason ?? '') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="invoice_type_document" class="form-label text-muted mb-0" id="lbl_invoice_type_document">Document Type and Number<span class="text-danger">*</span></label>
                                            <div class="d-flex">
                                                <div class="w-25">
                                                    <select name="invoice_type_document" id="invoice_type_document" class="form-select rounded-0 rounded-start">
                                                        <option value="" selected disabled>Select...</option>
                                                        <option value="ID Tax Payer" {{ old('invoice_type_document', $myinscription->invoice_type_document) == 'ID Tax Payer' ? 'selected' : '' }} >ID Tax Payer</option>
                                                        <option value="RUC" {{ old('invoice_type_document', $myinscription->invoice_type_document) == 'RUC' ? 'selected' : '' }}>RUC</option>
                                                        <option value="DNI" {{ old('invoice_type_document', $myinscription->invoice_type_document) == 'DNI' ? 'selected' : '' }} >DNI</option>
                                                        <option value="Passport" {{ old('invoice_type_document', $myinscription->invoice_type_document) == 'Passport' ? 'selected' : '' }} >Passport</option>
                                                        <option value="Other" {{ old('invoice_type_document', $myinscription->invoice_type_document) == 'Other' ? 'selected' : '' }} >Other</option>
                                                    </select>
                                                </div>
                                                <div class="w-75">
                                                    <input type="text" name="invoice_ruc" id="invoice_ruc" class="form-control no-spaces rounded-0 rounded-end" placeholder="Document Number" value="{{ old('invoice_ruc', $myinscription->invoice_ruc ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="invoice_address" class="form-label text-muted mb-0"><span id="lbl_invoice_address">Address</span> <span class="text-danger">*</span></label>
                                            <input type="text" name="invoice_address" id="invoice_address" class="form-control" placeholder="" maxlength="50" autocomplete="new-address" value="{{ old('invoice_address', $myinscription->invoice_address ?? '') }}">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12" id="dv_payment">
                                <div class="card px-3 py-3">
                                    <label for="" class="form-label fw-bold text-center">METHOD OF PAYMENT</label>


                                    <!-- RADIO OCULTO: NO PAYMENT -->
                                    <input type="radio" name="payment_method" value="none" id="payment_method_none" checked hidden>

                                    
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


                                    @if ($myinscription->payment_method == 'Bank Transfer/Wire')
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

                                    @if ($myinscription->payment_method == 'Credit/Debit Card' && $paymentcards->count() > 0)
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

                                </div>
                            </div>
                            
                            @if($myinscription->status == 'Draft' || $myinscription->status == 'Pending')
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-secondary btn-lg" name="action" value="save" id="btnSaveInscription">Save</button>
                                    <button type="submit" class="btn btn-primary btn-lg" name="action" value="register" id="btnSubInscription">Register Now</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>




            </div>
        </div>

    </div>

</div>

@endsection
