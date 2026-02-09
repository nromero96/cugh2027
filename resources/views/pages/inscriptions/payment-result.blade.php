<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Result | CUGH 2027</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon.ico')}}"/>
    <link href="{{asset('layouts/vertical-light-menu/css/light/loader.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('layouts/vertical-light-menu/css/dark/loader.css')}}" rel="stylesheet" type="text/css" />
    <script src="{{asset('layouts/vertical-light-menu/loader.js')}}"></script>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100;0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,100;1,9..40,400;1,9..40,500;1,9..40,700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{asset('layouts/vertical-light-menu/css/light/plugins.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/light/authentication/auth-cover.css')}}" rel="stylesheet" type="text/css" />
    
    <link href="{{asset('layouts/vertical-light-menu/css/dark/plugins.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/dark/authentication/auth-cover.css')}}" rel="stylesheet" type="text/css" />

</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">

                    {{-- Logo --}}
                    <div class="mb-4">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Event Logo" class="img-fluid" style="max-height: 80px;">
                    </div>

                    {{-- Payment Status --}}
                    @if($estado === 'AUTORIZADO')
                        <div class="mb-3">
                            <span class="badge bg-success fs-6 px-3 py-2">
                                Payment Successful
                            </span>
                        </div>
                    @else
                        <div class="mb-3">
                            <span class="badge bg-danger fs-6 px-3 py-2">
                                Payment Not Completed
                            </span>
                        </div>
                    @endif

                    {{-- Message --}}
                    <h4 class="mt-3">
                        {{ $mensaje ?? 'Your payment has been processed.' }}
                    </h4>

                    <hr class="my-4">

                    {{-- Event Info --}}
                    <h5 class="fw-bold">
                        CUGH’s 18th Annual Conference
                    </h5>
                    <p class="mb-1">
                        <strong>Lima – Peru</strong>
                    </p>
                    <p class="text-muted">
                        February 25–28, 2027
                    </p>

                    <hr class="my-4">

                    {{-- Inscription Info --}}
                    <p class="mb-4">
                        <strong>Registration Number:</strong> #{{ $inscription_id }}
                    </p>

                    {{-- Button --}}
                    <a href="{{ route('inscriptions.show', $inscription_id) }}"
                       class="btn btn-primary">
                        View My Registration
                    </a>

                </div>
            </div>

            {{-- Footer --}}
            <p class="text-center text-muted mt-4 small">
                If you have any questions, please contact our support team.
            </p>

        </div>
    </div>
</div>

</body>
</html>
