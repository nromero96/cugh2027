@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <!-- BREADCRUMB -->
        <div class="page-meta">
            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('users.index')}}">{{__("Usuarios")}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__("Editar")}}</li>
                </ol>
            </nav>
        </div>
        <!-- /BREADCRUMB -->

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-2">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4>{{__("Información del usuario")}}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <form class="row g-3" action="{{ route('users.index').'/'.$user->id }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="col-md-4">
                                <label for="salutation" class="form-label text-muted mb-0">Salutation <span class="text-danger">*</span></label>
                                <select name="salutation" id="salutation" class="form-control @error('salutation') is-invalid @enderror">
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
                                <input type="text" class="form-control convert_mayus @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                                {!!$errors->first("name", "<span class='text-danger'>:message</span>")!!}
                            </div>
                            <div class="col-md-4">
                                <label for="inputLastName" class="form-label text-muted mb-0">Middle Name</label>
                                <input type="text" class="form-control convert_mayus @error('lastname') is-invalid @enderror" name="lastname" id="lastname" value="{{ old('lastname', $user->lastname) }}">
                                {!!$errors->first("lastname", "<span class='text-danger'>:message</span>")!!}
                            </div>
                            <div class="col-md-4">
                                <label for="inputSecondLastName" class="form-label text-muted mb-0">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control convert_mayus @error('second_lastname') is-invalid @enderror" name="second_lastname" id="second_lastname" value="{{ old('second_lastname', $user->second_lastname) }}" required>
                                {!!$errors->first("second_lastname", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            {{-- degrees --}}
                            <div class="col-md-4">
                                <label for="inputDegrees" class="form-label text-muted mb-0">Degrees <span class="text-danger">*</span></label>
                                <select name="degrees" id="inputDegrees" class="form-select @error('degrees') is-invalid @enderror">
                                    <option value="" {{ old('degrees', $user->degree) == '' ? 'selected' : '' }}>Select...</option>
                                    <option value="Graduate" {{ old('degrees', $user->degrees) == 'Graduate' ? 'selected' : '' }}>Graduate</option>
                                    <option value="Master" {{ old('degrees', $user->degrees) == 'Master' ? 'selected' : '' }}>Master</option>
                                    <option value="PhD" {{ old('degrees', $user->degrees) == 'PhD' ? 'selected' : '' }}>PhD</option>
                                    <option value="Other" {{ old('degrees', $user->degrees) == 'Other' ? 'selected' : '' }}>Other (Please specify)</option>
                                </select>
                                {!!$errors->first("degrees", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4 @if(old('degrees', $user->degrees) == 'Other') d-block @else d-none @endif" id="other_degrees_div">
                                <label for="other_degrees" class="form-label text-muted mb-0">Other Degree <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('other_degrees') is-invalid @enderror" name="other_degrees" id="other_degrees" value="{{ old('other_degrees', $user->other_degrees) }}">
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
                                <select name="cugh_member_institution" id="cugh_member_institution" class="form-select @error('cugh_member_institution') is-invalid @enderror">
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
                                <input type="text" class="form-control convert_mayus @error('job_title') is-invalid @enderror" name="job_title" id="job_title" value="{{ old('job_title', $user->job_title) }}">
                                {!!$errors->first("job_title", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputDocumentType" class="form-label text-muted mb-0">Document Type <span class="text-danger">*</span></label>
                                <select name="document_type" class="form-select @error('document_type') is-invalid @enderror" id="inputDocumentType" required>
                                    <option value="" {{ old('document_type', $user->document_type) == '' ? 'selected' : '' }}>Select...</option>
                                    <option value="DNI" {{ old('document_type', $user->document_type) == 'DNI' ? 'selected' : '' }}>DNI (for Peruvian citizens only)</option>
                                    <option value="Passport" {{ old('document_type', $user->document_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
                                </select>
                                {!!$errors->first("document_type", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputDocumentNumber" class="form-label text-muted mb-0">Document Number <span class="text-danger">*</span></label>
                                <input type="text" name="document_number" class="form-control no-spaces @error('document_number') is-invalid @enderror" id="inputDocumentNumber" value="{{ old('document_number', $user->document_number) }}" required>
                                {!!$errors->first("document_number", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputNationality" class="form-label text-muted mb-0">Nationality  <span class="text-danger">*</span></label>
                                <select name="nationality" class="form-select @error('nationality') is-invalid @enderror" id="inputNationality">
                                    <option value="" disabled selected>Select...</option>
                                    @foreach ($countries as $nationality)
                                        <option value="{{$nationality->id}}" {{ old('nationality', $user->nationality) == $nationality->id ? 'selected' : '' }}>{{$nationality->name}}</option>
                                    @endforeach
                                </select>
                                {!!$errors->first("nationality", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputGender" class="form-label text-muted mb-0">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" id="inputGender">
                                    <option value="" {{ old('gender', $user->gender) == '' ? 'selected' : '' }}>Select...</option>
                                    <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                {!!$errors->first("gender", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="inputOccupation" class="form-label text-muted mb-0">Occupation <span class="text-danger">*</span></label>
                                <select name="occupation" class="form-select @error('occupation') is-invalid @enderror" id="inputOccupation">
                                    <option value="" {{ old('occupation', $user->occupation) == '' ? 'selected' : '' }}>Select...</option>
                                    <option value="Business" {{ old('occupation', $user->occupation) == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="Legal" {{ old('occupation', $user->occupation) == 'Legal' ? 'selected' : '' }}>Legal</option>
                                    <option value="Education" {{ old('occupation', $user->occupation) == 'Education' ? 'selected' : '' }}>Education</option>
                                    <option value="Health Care" {{ old('occupation', $user->occupation) == 'Health Care' ? 'selected' : '' }}>Health Care</option>
                                    <option value="Media" {{ old('occupation', $user->occupation) == 'Media' ? 'selected' : '' }}>Media</option>
                                    <option value="Science and Technology" {{ old('occupation', $user->occupation) == 'Science and Technology' ? 'selected' : '' }}>Science and Technology</option>
                                    <option value="Other" {{ old('occupation', $user->occupation) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                {!!$errors->first("occupation", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4 @if ($user->occupation != 'Other') d-none @endif " id="occupation_other">
                                <label for="inputOtherOccupation" class="form-label text-muted mb-0">Other Occupation <span class="text-danger">*</span></label>
                                <input type="text" name="occupation_other" class="form-control @error('occupation_other') is-invalid @enderror" id="inputOtherOccupation" value="{{ old('occupation_other', $user->occupation_other) }}">
                                {!!$errors->first("occupation_other", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-12">
                                    <hr class="mt-1 mb-1">
                                    <div class="row">
                                        <div class="col-md-4 mt-3">
                                            <label for="inputWorkplace" class="form-label text-muted mb-0">Workplace <span class="text-danger">*</span></label>
                                            <input type="text" name="workplace" class="form-control @error('workplace') is-invalid @enderror" id="inputWorkplace" value="{{ old('workplace', $user->workplace) }}">
                                            {!!$errors->first("workplace", "<span class='text-danger'>:message</span>")!!}
                                        </div>

                                        <div class="col-md-8 mt-3">
                                            <label for="inputAddress" class="form-label text-muted mb-0">Work Address <span class="text-danger">*</span></label>
                                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" id="inputAddress" value="{{ old('address', $user->address) }}" maxlength="50" autocomplete="new-work-address">
                                            {!!$errors->first("address", "<span class='text-danger'>:message</span>")!!}
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label for="inputCity" class="form-label text-muted mb-0">City <span class="text-danger">*</span></label>
                                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" id="inputCity" value="{{ old('city', $user->city) }}">
                                            {!!$errors->first("city", "<span class='text-danger'>:message</span>")!!}
                                        </div>


                                        <div class="col-md-4 mt-3">
                                            <label for="inputState" class="form-label text-muted mb-0">State <span class="text-danger">*</span></label>
                                            <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" id="inputState" value="{{ old('state', $user->state) }}">
                                            {!!$errors->first("state", "<span class='text-danger'>:message</span>")!!}
                                        </div>
                                        

                                        <div class="col-md-4 mt-3">
                                            <label for="inputCountry" class="form-label text-muted mb-0">Country  <span class="text-danger">*</span></label>
                                            <select name="country" class="form-select @error('country') is-invalid @enderror" id="inputCountry" required>
                                                <option value="" disabled selected>Select...</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{$country->id}}" @if (old('country', $user->country) == $country->id) selected="selected" @endif >{{$country->name}}</option>
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
                                        <select name="work_phone_code" class="form-select rounded-0 rounded-start @error('work_phone_code') is-invalid @enderror" id="inputPhoneCode">
                                            <option value="" disabled selected>_ _</option>
                                            @foreach ($countries as $country)
                                                <option value="{{$country->phone}}" {{ old('work_phone_code', $user->work_phone_code) == $country->phone ? 'selected' : '' }} >+{{$country->phone}} ({{$country->name}})</option>
                                            @endforeach
                                        </select>
                                        <small>Country</small>
                                    </div>
                                    <div class="w-25">
                                        <input type="text" name="work_phone_code_city" class="form-control no-spaces rounded-0 inputNumber @error('work_phone_code_city') is-invalid @enderror" id="inputWorkPhoneCodeCity" placeholder="_ _" maxlength="5" value="{{ old('work_phone_code_city', $user->work_phone_code_city) }}">
                                        <small>Area code</small>
                                    </div>
                                    <div class="w-50">
                                        <input type="text" name="work_phone_number" class="form-control no-spaces rounded-0 rounded-end inputNumber @error('work_phone_number') is-invalid @enderror" id="inputWorkPhoneNumber" placeholder="_ _ _ _ _ _ _ _" maxlength="12" value="{{ old('work_phone_number', $user->work_phone_number) }}">
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
                                        <select name="phone_code" class="form-select rounded-0 rounded-start @error('phone_code') is-invalid @enderror" id="inputPhoneCode">
                                            <option value="" disabled selected>_ _</option>
                                            @foreach ($countries as $country)
                                                <option value="{{$country->phone}}" {{ old('phone_code', $user->phone_code) == $country->phone ? 'selected' : '' }}>+{{$country->phone}} ({{$country->name}})</option>
                                            @endforeach
                                        </select>
                                        <small>Country</small>
                                    </div>
                                    <div class="w-75">
                                        <input type="text" name="phone_number" class="form-control no-spaces rounded-0 rounded-end inputNumber @error('phone_number') is-invalid @enderror" id="inputPhoneNumber" placeholder="_ _ _ _ _ _ _ _" maxlength="12" value="{{ old('phone_number', $user->phone_number) }}">
                                        <small>Number</small>
                                    </div>
                                </div>
                                {!!$errors->first("phone_code", "<span class='text-danger'>:message</span><br>")!!}
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
                                        <input type="text" name="whatsapp_number" class="form-control no-spaces rounded-0 rounded-end inputNumber @error('whatsapp_number') is-invalid @enderror" id="inputPhoneNumber" placeholder="_ _ _ _ _ _ _ _" maxlength="12" value="{{ old('whatsapp_number', $user->whatsapp_number) }}">
                                        <small>Number</small>
                                    </div>
                                </div>
                                {!!$errors->first("whatsapp_code", "<span class='text-danger'>:message</span>")!!}
                                {!!$errors->first("whatsapp_number", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            

                            <div class="col-md-6">
                                <label for="inputEmail" class="form-label text-muted mb-0">E-mail <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="inputEmail" value="{{ old('email', $user->email) }}" style="color: #383838;" readonly>
                                {!!$errors->first("email", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-6">
                                <label for="inputCcEmail" class="form-label text-muted mb-0">Cc E-mail</label>
                                <input type="email" name="cc_email" class="form-control @error('cc_email') is-invalid @enderror" id="inputCcEmail" value="{{ old('cc_email', $user->cc_email) }}">
                                {!!$errors->first("cc_email", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-6">
                                <label for="inputSolapin" class="form-label text-muted mb-0">Conference badge <span class="text-danger">*</span> <small class="fw-normal">(A first and last name)</small></label>
                                <div class="d-flex">
                                    <input type="text" class="form-control convert_mayus @error('solapin_name') is-invalid @enderror" name="solapin_name" id="inputSolapin" value="{{ old('solapin_name', $user->solapin_name) }}" placeholder="First Name" >
                                    <input type="text" class="form-control convert_mayus @error('solapin_lastname') is-invalid @enderror" name="solapin_lastname" id="inputSolapin" value="{{ old('solapin_lastname', $user->solapin_lastname) }}" placeholder="Last Name" >
                                </div>
                                {!!$errors->first("solapin_name", "<span class='text-danger'>:message</span><br>")!!}
                                {!!$errors->first("solapin_lastname", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-6"></div>

                            <div class="col-md-6">
                                <label for="inputPassword" class="form-label text-muted mb-0">{{__("Contraseña")}}</label>
                                <input type="password" name="password" class="form-control" id="inputPassword" placeholder="●●●●●●" autocomplete="new-password">
                                {!!$errors->first("password", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-12">
                                <label for="roleuser" class="form-label fw-bold">{{__("Rol")}}</label>
                                <br>
                                @php
                                    if(!empty($user->getRoleNames())){
                                        foreach ($user->getRoleNames() as $name) {
                                            $namerole = $name;
                                        }
                                    }
                                @endphp
                                @foreach ($roles as $item)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input cursor-pointer" type="radio" name="roles[]" id="inlineRadio{{$item->id}}" value="{{$item->id}}"  @if ($item->name == $namerole) checked @endif>
                                        <label class="form-check-label cursor-pointer" for="inlineRadio{{$item->id}}">{{$item->name}}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                <label for="inputPhoto" class="form-label fw-bold">{{__("Foto")}}</label>
                                <input type="file" name="photo" class="form-control" id="inputPhoto">
                            </div>
                            <div class="col-md-6">
                                <img src="{{ asset('storage/uploads/profile_images').'/'.$user->photo}}" class="rounded" width="70px" height="70px">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="inputStatus" class="form-label fw-bold">{{__("Estado")}}</label><br>
                                <div class="switch form-switch-custom switch-inline form-switch-primary">
                                    <input type="hidden" name="status" value="inactive">
                                    <input type="checkbox" name="status" class="switch-input" role="switch" id="form-status-switch-checked" value="active" {{$user->status == 'active' ? 'checked' : ''}}>
                                </div>
                            </div>
                            <div class="col-12">
                                @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                                    <button type="submit" class="btn btn-primary disabled">{{__("Update")}}</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


@endsection