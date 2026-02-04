@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 mb-2 col-12">
                                <h4>
                                    My personal information
                                    @php
                                    $namerole = '';
                                    if(!empty($user->getRoleNames())){
                                        foreach ($user->getRoleNames() as $name) {
                                            $namerole = $name;
                                        }
                                    }
                                @endphp
                                <span class="badge badge-light-dark float-end">{{$namerole}}</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        
                        @if ($user->confir_information)
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">First Name</label>
                                <br>
                                <span class="bx-text">{{$user->name}}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Middle Name</label>
                                <br>
                                <span class="bx-text">{{$user->lastname}}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Last Name</label>
                                <br>
                                <span class="bx-text">{{$user->second_lastname}}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Document type</label>
                                <br>
                                <span class="bx-text">{{$user->document_type}}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Document number</label>
                                <br>
                                <span class="bx-text">{{$user->document_number}}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Nationality</label>
                                <br>
                                <span class="bx-text">{{$user->nationality}}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Gender</label>
                                <br>
                                <span class="bx-text">{{$user->gender}}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Occupation</label>
                                <br>
                                <span class="bx-text">{{$user->occupation}}</span>
                            </div>

                            <div class="col-md-4 @if($user->occupation != 'Other') d-none @else @endif">
                                <label class="form-label fw-bold">Other Occupation</label>
                                <br>
                                <span class="bx-text">{{$user->occupation_other}}</span>
                            </div>

                            <div class="col-md-12">
                                <hr class="mt-1 mb-0">
                                    <div class="row">
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold">Workplace</label>
                                            <br>
                                            <span class="bx-text">{{$user->workplace}}</span>
                                        </div>
                                        <div class="col-md-8 mt-3">
                                            <label class="form-label fw-bold">Work Address</label>
                                            <br>
                                            <span class="bx-text">{{$user->address}}</span>
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold">City</label>
                                            <br>
                                            <span class="bx-text">{{$user->city}}</span>
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold">State</label>
                                            <br>
                                            <span class="bx-text">{{$user->state}}</span>
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold">Country</label>
                                            <br>
                                            <span class="bx-text">{{$user->country}}</span>
                                        </div>
                                    </div>
                                <hr class="mt-3 mb-1">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Phone</label>
                                <div class="d-flex">
                                    <div class="w-25">
                                        <span class="bx-text me-1">{{$user->phone_code}}</span>
                                        <small>Country</small>
                                    </div>
                                    <div class="w-25">
                                        <span class="bx-text me-1">{{$user->phone_code_city}}</span>
                                        <small>Area code</small>
                                    </div>
                                    <div class="w-50">
                                        <span class="bx-text">{{$user->phone_number}}</span>
                                        <small>Number</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{__("WhatsApp")}}</label>
                                <div class="d-flex">
                                    <div class="w-25">
                                        <span class="bx-text me-1">{{$user->whatsapp_code}}</span>
                                        <small>Country</small>
                                    </div>
                                    <div class="w-75">
                                        <span class="bx-text">{{$user->whatsapp_number}}</span>
                                        <small>Number</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">E-mail</label>
                                <span class="bx-text">{{$user->email}}</span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Conference badge: <small class="fw-normal">(A first and last name)</small></label>
                                <span class="bx-text">{{$user->solapin_name}} {{$user->solapin_lastname}}</span>

                            </div>
                        </div>
                        @else
                            <div class="alert alert-danger">
                                Please complete your registration in the <a href="{{route('inscriptions.myinscription')}}">My Inscription</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


@endsection