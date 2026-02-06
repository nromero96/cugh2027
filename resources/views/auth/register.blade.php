<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Register | CUGH 2027</title>
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon.ico')}}"/>
    <link href="{{asset('layouts/vertical-light-menu/css/light/loader.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('layouts/vertical-light-menu/css/dark/loader.css')}}" rel="stylesheet" type="text/css" />
    <script src="{{asset('layouts/vertical-light-menu/loader.js')}}"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100;0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,100;1,9..40,400;1,9..40,500;1,9..40,700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{asset('layouts/vertical-light-menu/css/light/plugins.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/light/authentication/auth-cover.css')}}" rel="stylesheet" type="text/css" />
    
    <link href="{{asset('layouts/vertical-light-menu/css/dark/plugins.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/dark/authentication/auth-cover.css')}}" rel="stylesheet" type="text/css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- END GLOBAL MANDATORY STYLES -->
    
</head>
<body class="form">

    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->

    <div class="auth-container d-flex">

        <div class="container mx-auto align-self-center">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row">
                    <div class="col-6 d-lg-flex d-none h-100 my-auto top-0 start-0 text-center justify-content-center flex-column">
                        <div class="auth-cover-bg-image"></div>
                        <div class="auth-overlay" style="background-image: url({{asset('assets/img/bg-lg-1-min.jpg')}});background-size: cover; background-position: center"></div>
                        <div class="auth-cover">
                            <div class="position-relative">
                                <h2 class="mt-5 text-white px-2" style="font-weight: bold;">CUGH’s 18th ANNUAL CONFERENCE<br> LIMA – PERU</h2>
                                <p class="text-white">February 25-28, 2027</p>
                            </div>
                        </div>

                    </div>

                    <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center ms-lg-auto me-lg-0 mx-auto">
                        <div class="card">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        {{-- <div class="text-end">
                                            <a href="locale/es" class="" ><span class="badge {{ (app()->getLocale() == 'es') ? 'badge-light-primary' : 'badge-light-dark' }}">ES</span></span></a>
                                            <a href="locale/en" class="" ><span class="badge {{ (app()->getLocale() == 'en') ? 'badge-light-primary' : 'badge-light-dark' }}">EN</span></span></a>
                                        </div> --}}
                                        <h2>Register</h2>
                                        <p>Complete your information to register.</p>
                                    </div>

                                    @if(session('error'))
                                        <div class="alert alert-danger" role="alert">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="email" class="form-label mb-0">E-mail</label>
                                            <input id="email" type="email" class="form-control email-smart @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" list="email-domains" required autocomplete="email">
                                            <datalist id="email-domains">
                                                <!-- Se llena dinámicamente -->
                                            </datalist>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="" class="form-label mb-0">Type and Number of Document</label>
                                        <div class="input-group mb-3">
                                            <select class="form-select" id="document_type" name="document_type" required style="width: 40%;">
                                                <option value="">Select...</option>
                                                <option value="DNI" @if(old('document_type') == 'DNI') selected @endif>DNI (for Peruvian citizens only)</option>
                                                <option value="Passport" @if(old('document_type') == 'Passport') selected @endif>Passport</option>
                                            </select>
                                            <input type="text" class="form-control no-spaces" id="document_number" name="document_number" value="{{ old('document_number') }}" required style="width: 60%;">
                                            
                                            @error('document_number')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="password" class="form-label mb-0">Password</label>
                                            <div class="input-group">
                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                                <span class="input-group-text cursor-pointer"
                                                    onclick="togglePassword('password', this)">
                                                    <i class="bi bi-eye"></i>
                                                </span>
                                            </div>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label for="password-confirm" class="form-label mb-0">Confirm Password</label>
                                            <div class="input-group">
                                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                                <span class="input-group-text cursor-pointer"
                                                    onclick="togglePassword('password-confirm', this)">
                                                    <i class="bi bi-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-secondary w-100">
                                                Register
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <div class="text-center">
                                            <p class="mb-0">Do you already have an account? <a href="{{ route('login') }}" class="text-warning">Enter Here.</a></p>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="text-center">
                                            <p class="mb-0"><a href="/password-recovery" class="text-info">Forgot your password?</a></p>
                                        </div>
                                    </div>

                                    @if (session('status'))
                                        <div class="alert alert-danger mt-2" role="alert" id="status-alert">
                                            {{ session('status') }}
                                        </div>

                                        <script>
                                            setTimeout(function() {
                                                document.getElementById('status-alert').remove();
                                            }, 5000); // 10000 milisegundos = 10 segundos
                                        </script>
                                    @endif



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="{{asset('bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.no-spaces').forEach(input => {

                // Elimina espacios en tiempo real (teclado y pegar)
                input.addEventListener('input', () => {
                    input.value = input.value.replace(/\s/g, '');
                });

                // Bloquea la barra espaciadora
                input.addEventListener('keydown', (e) => {
                    if (e.key === ' ') {
                        e.preventDefault();
                    }
                });

                // Limpia espacios al pegar
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const text = (e.clipboardData || window.clipboardData)
                                    .getData('text')
                                    .replace(/\s/g, '');
                    document.execCommand('insertText', false, text);
                });

            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const emailInput = document.querySelector('.email-smart');
            const datalist   = document.getElementById('email-domains');

            const domains = [
                'gmail.com',
                'outlook.com',
                'hotmail.com',
                'yahoo.com',
                'icloud.com'
            ];

            emailInput.addEventListener('input', () => {
                // 1️⃣ Convertir a minúsculas
                emailInput.value = emailInput.value.toLowerCase();

                // 2️⃣ Eliminar espacios
                emailInput.value = emailInput.value.replace(/\s/g, '');

                // 3️⃣ Autocompletar dominios
                datalist.innerHTML = '';

                const value = emailInput.value;
                const atIndex = value.indexOf('@');

                if (atIndex > -1) {
                    const prefix = value.substring(0, atIndex + 1);
                    const typedDomain = value.substring(atIndex + 1);

                    domains.forEach(domain => {
                        if (domain.startsWith(typedDomain)) {
                            const option = document.createElement('option');
                            option.value = prefix + domain;
                            datalist.appendChild(option);
                        }
                    });
                }
            });

            // 4️⃣ Bloquear espacio desde teclado
            emailInput.addEventListener('keydown', (e) => {
                if (e.key === ' ') e.preventDefault();
            });

            // 5️⃣ Limpiar espacios al pegar
            emailInput.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData)
                                .getData('text')
                                .toLowerCase()
                                .replace(/\s/g, '');
                document.execCommand('insertText', false, text);
            });

        });
        </script>



    <script>
        function togglePassword(inputId, el) {
            const input = document.getElementById(inputId);
            const icon  = el.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>


</body>
</html>