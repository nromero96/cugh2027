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

                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-10 col-md-10 col-sm-10 mb-2 col-10">
                                <h4>
                                    Panel # {{ $panel->id }}
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div class="row g-3" id="print_work">
                            <div class="col-md-12">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Language:</label>
                                <p class="">{{ $panel->language }}</p>
                            </div>
                            <div class="col-md-12">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Sub-Themes:</label>
                                <p class="">
                                    @if($panel->subthemes)

                                            @foreach($panel->subthemes as $subtheme)

                                                <span style="display:inline-block;margin:3px;">
                                                    * {{ $subtheme }}
                                                </span>

                                            @endforeach

                                        @endif

                                        @if($panel->subthemes_other)

                                            <span style="margin-top:10px;">
                                                <strong>Other:</strong><br>
                                                {{ $panel->subthemes_other }}
                                            </span>

                                        @endif
                                </p>
                            </div>

                            <div class="col-md-12">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Title:</label>
                                <p class="">{{ $panel->title }}</p>
                            </div>

                            
                            <div class="col-md-12">
                                <hr class="my-0">
                                <h5 class="mb-0 mt-2">{{__("Contact person")}}</h5>
                            </div>

                            <div class="col-md-2">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Salutation:</label>
                                <p class="">{{ $panel->contact_salutation }}</p>
                            </div>

                            <div class="col-md-5">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Full Name:</label>
                                <p class="">{{ $panel->contact_name }}</p>
                            </div>

                            <div class="col-md-5">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Institution:</label>
                                <p class="">{{ $panel->contact_institution }}</p>
                            </div>

                            <div class="col-md-2">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Country:</label>
                                <p class="">{{ $panel->contact_country }}</p>
                            </div>

                            <div class="col-md-5">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Cellphone:</label>
                                <p class="">{{ $panel->contact_phone }}</p>
                            </div>

                            <div class="col-md-5">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">E-mail:</label>
                                <p class="">{{ $panel->contact_email }}</p>
                            </div>

                            <div class="col-md-12">
                                <hr class="my-0">
                                <h5 class="mb-0 mt-2">{{__("Moderator")}}</h5>
                            </div>

                            <div class="col-md-6">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Name:</label>
                                <p class="">{{ $panel->moderator_name }}</p>
                            </div>

                            <div class="col-md-6">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Position:</label>
                                <p class="">{{ $panel->moderator_position }}</p>
                            </div>

                            <div class="col-md-6">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Institution:</label>
                                <p class="">{{ $panel->moderator_institution }}</p>
                            </div>

                            <div class="col-md-6">
                                <label for="inputAreaConocimento" class="form-label fw-bold m-0">Country:</label>
                                <p class="">{{ $panel->moderator_country }}</p>
                            </div>

                            <div class="col-md-12">
                                <hr class="my-0">
                                <h5 class="mb-0 mt-2">{{__("Speakers")}}</h5>
                            </div>

                            <div class="col-md-12">
                                @if($panel->speakers)

                                    @foreach($panel->speakers as $speaker)

                                        <div style="border:1px solid #e5e7eb;border-radius:12px;padding:10px;margin-bottom:10px;background:#fafafa;">

                                            <table width="100%" cellpadding="0" cellspacing="0">

                                                <tr>
                                                    <td width="160"><strong>Name:</strong></td>
                                                    <td>{{ $speaker['name'] ?? '' }}</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Position:</strong></td>
                                                    <td>{{ $speaker['position'] ?? '' }}</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Institution:</strong></td>
                                                    <td>{{ $speaker['institution'] ?? '' }}</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Country:</strong></td>
                                                    <td>{{ $speaker['country'] ?? '' }}</td>
                                                </tr>

                                            </table>

                                        </div>

                                    @endforeach

                                @else

                                    <p>No speakers added.</p>

                                @endif
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold mb-0">Panel Description:</label>
                                <p class="">{!! nl2br(e($panel->description)) !!}</p>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold mb-0">Learning Objectives:</label>
                                <p>{!! nl2br(e($panel->learning_objectives)) !!}</p>
                            </div>

                            {{-- Print Pdf Administrator --}}
                            @if(\Auth::user()->hasRole('Administrador'))
                                <div class="col-md-12 text-end">
                                    <a href="{{ route('panels.edit', $panel->id) }}" class="btn btn-outline-primary">Edit Panel</a>
                                    <a href="{{ route('panels.pdf', $panel->id) }}" class="btn btn-primary" target="_blank">Print PDF</a>
                                </div>
                            @endif

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


@endsection
