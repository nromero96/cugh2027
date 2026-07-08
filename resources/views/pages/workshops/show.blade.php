@extends('layouts.app')

@section('content')

<style>
@media print {

    /* Ocultar botones */
    .no-print {
        display: none !important;
    }

    /* Opcional: quitar márgenes raros */
    body {
        margin: 0;
    }

    /* Opcional: ajustar contenido */
    .layout-px-spacing {
        padding: 0 !important;
    }

    .main-content{
        margin-top: 0 !important;
    }

}
</style>

<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-6 mb-2 col-6">
                                <h4>
                                    Workshop N°: {{ $workshop->id }} 

                                </h4>
                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-6 mb-2 col-6 text-end">
                                <span class="badge bg-light-info mt-2">Created: {{ $workshop->created_at }}</span>
                                <span class="d-block mt-2">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                            <form class="row g-3">
                                <div class="col-md-12">
                                    <h6 class="fw-bold">Lead Contact Person</h6>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Name:</b>
                                    <span class="text-muted">{{ $workshop->lead_name }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Institution:</b>
                                    <span class="text-muted">{{ $workshop->lead_institution }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Professional Title:</b>
                                    <span class="text-muted">{{ $workshop->lead_title }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">E-mail:</b>
                                    <span class="text-muted">{{ $workshop->lead_email }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Phone number (with country & area code):</b>
                                    <span class="text-muted">{{ $workshop->lead_phone }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Cell Phone (with country code):</b>
                                    <span class="text-muted">{{ $workshop->lead_cell }}</span>
                                </div>

                                <div class="col-md-12">
                                    <hr class="mt-0">
                                    <h6 class="fw-bold">Workshop Program Description</h6>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Workshop Title:</b>
                                    <span class="text-muted">{{ $workshop->workshop_title }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Workshop Description (max 200 words):</b>
                                    <span class="text-muted">{!! nl2br(e($workshop->workshop_desc)) !!}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Objectives / Skills:</b>
                                    <span class="text-muted">{!! nl2br(e($workshop->workshop_objectives)) !!}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Speakers and Facilitators:</b>
                                    <span class="text-muted">{!! nl2br(e($workshop->workshop_speakers)) !!}</span>
                                </div>

                                <div class="col-md-12">
                                    <hr class="mt-0">
                                    <h6 class="fw-bold">Workshop Room Options</h6>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Preferred Time Slot:</b>
                                    <span class="text-muted">{{ $workshop->time_slot }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Half or Full Day:</b>
                                    <span class="text-muted">{{ $workshop->day_length }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Preferred Room Set-up:</b>
                                    <span class="text-muted">{{ $workshop->room_setup }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Desired Number of Attendees:</b>
                                    <span class="text-muted">{{ $workshop->attendees }}</span>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Notes or Comments:</b>
                                    <span class="text-muted">{!! nl2br(e($workshop->notes)) !!}</span>
                                </div>

                                <div class="col-md-12">
                                    <hr class="mt-0">
                                    <h6 class="fw-bold">Contact for Invoice</h6>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <b class="form-label mb-0 d-block text-black">Will the applying party be the lead contact person for payment?:</b>
                                    <span class="text-muted">{{ $workshop->payment_lead_same ? 'Yes' : 'No' }}</span>
                                </div>

                                <div class="col-md-12 mt-5">
                                    <p><img src="{{ asset('storage/uploads/workshops').'/'.$workshop->signature_path}}" alt="CUGH 2027" style="width: 300px;"></p>
                                    <span class="text-muted">{{ $workshop->place_date }}</span>
                                </div>

                                <div class="col-12 text-end no-print">
                                    <a href="{{ route('workshops.index') }}" class="btn btn-outline-secondary">Back</a>
                                    <a href="{{ route('workshops.pdf', $workshop->id) }}" target="_blank" class="btn btn-primary">Print</a>
                                </div>
                            </form>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
function printSection(className) {
    const content = document.querySelector('.' + className).innerHTML;

    const printWindow = window.open('', '', 'width=800,height=600');

    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .no-print { display: none !important; }
                sup { font-size: 0.7em; }
            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}
</script>

@endsection