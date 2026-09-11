<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submissions Closed</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        body { background: #f5f7fb; }
        .closed-card { max-width: 760px; }
        .closed-icon { width: 72px; height: 72px; }
    </style>
</head>
<body>
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <section class="card closed-card w-100 border-0 shadow-sm text-center" aria-labelledby="closed-title">
            <div class="card-body px-4 py-5">
                <div class="closed-icon rounded-circle bg-light-danger text-danger d-inline-flex align-items-center justify-content-center mb-4" aria-hidden="true">
                    <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M8 8l8 8M16 8l-8 8"></path>
                    </svg>
                </div>
                <h1 id="closed-title" class="h3 fw-bold mb-3">{{ $message }}</h1>
                <p class="text-muted mb-4">New submissions are no longer being accepted.</p>
                <a href="https://cughlima2027.org/" class="btn btn-primary">Return to Home</a>
            </div>
        </section>
    </main>
</body>
</html>
