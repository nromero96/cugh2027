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


                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-6 mb-2 col-6">
                                <h4>
                                    Abstract N°: {{ $abstract_post->id }} 
                                    @if ($abstract_post->status == 'draft')
                                        <span class="badge bg-light-warning mt-2">Draft</span>
                                    @elseif ($abstract_post->status == 'submitted')
                                        <span class="badge bg-light-info mt-2">Submitted</span>
                                    @elseif ($abstract_post->status == 'rejected')
                                        <span class="badge bg-light-danger mt-2">Rejected</span>
                                    @endif
                                </h4>
                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-6 mb-2 col-6 text-end">
                                <span class="badge bg-light-secondary mt-2">Last Update: {{ $abstract_post->updated_at }}</span>
                                <span class="d-block mt-2">{{ optional($user->residenceCountry)->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <p class="text-black">{{ $abstract_post->presentation_type }}</p>
                                </div>

                                <div class="col-md-12">
                                    <label for="optionsAbstractType" class="form-label text-muted mb-0 d-block">Abstract Type:</label>
                                    <p class="text-black">{{ $abstract_post->abstract_type }}</p>
                                </div>

                                <div class="col-md-12">
                                    <label for="selectSubtopic" class="form-label text-muted mb-0 d-block">Sub theme:</label>
                                    <p class="text-black"">{{ $abstract_post->subtopic }}</p>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="inputName" class="form-label text-muted mb-0 d-block">Title:</label>
                                    <p class="text-black fw-bold">{{ $abstract_post->title }}</p>
                                </div>


                                <div class="col-md-12">
                                    @php
                                        $coAuthors = collect(json_decode($abstract_post->co_authors, true));
                                        $institutions = collect(json_decode($abstract_post->institutions, true));

                                        // Asignar número a cada institución
                                        $institutions = $institutions->values()->map(function($inst, $index){
                                            $inst['number'] = $index + 1;
                                            return $inst;
                                        });

                                        // Mapear coautor => números de instituciones
                                        $coAuthorsMapped = $coAuthors->map(function($ca) use ($institutions) {
                                            $numbers = [];

                                            foreach ($institutions as $inst) {
                                                if (in_array($ca['id'], $inst['coauthors'] ?? [])) {
                                                    $numbers[] = $inst['number'];
                                                }
                                            }

                                            $ca['institutions'] = $numbers;
                                            return $ca;
                                        });
                                    @endphp

                                    <p class="text-black mb-4">
                                        @foreach($coAuthorsMapped as $ca)
                                            <span>
                                                {{ $ca['name'] }} {{ $ca['lastname'] }}
                                                @if(count($ca['institutions']))
                                                    <sup>{{ implode(',', $ca['institutions']) }}</sup>
                                                @endif
                                            </span><br>
                                        @endforeach
                                    </p>

                                    <p class="text-black fst-italic">
                                        @foreach($institutions as $inst)
                                            <span>
                                                <sup>{{ $inst['number'] }}</sup> {{ $inst['name'] }}
                                            </span>
                                            @if(!$loop->last), @endif
                                        @endforeach
                                    </p>

                                </div>

                                <div class="col-md-12">
                                    <label for="inputDescription" class="form-label text-muted d-block mb-0">
                                        Body text:
                                    </label>
                                    <p class="text-black">
                                        {!! nl2br(e($abstract_post->body)) !!}
                                    </p>
                                </div>

                                <div class="col-md-12">
                                    <label for="inputKeywords" class="form-label text-muted mb-2 d-block">Keywords:</label>
                                    <p class="text-black">
                                            @php
                                            $keywords = json_decode($abstract_post->keywords, true);
                                            @endphp
                                            @if(is_array($keywords))
                                                @foreach($keywords as $keyword)
                                                    <span class="tag">{{ $keyword }}</span>@if(!$loop->last), @endif
                                                @endforeach
                                            @endif
                                    </p>
                                </div>

                                <div class="col-12 text-end no-print">
                                    <a href="{{ route('abstract_posts.index') }}" class="btn btn-outline-secondary">Back</a>
                                    <a href="#" onclick="window.print()" class="btn btn-primary">Print</a>
                                </div>
                            </div>
                    </div>
                </div>

                <div class="statbox widget box box-shadow mt-3">
                    <div class="widget-header py-2 px-3">


                        @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                            <div class="row">
                                <div class="col-12">
                                    <h6>Add comment and status</h6>
                                </div>
                            </div>

                            <form class="row" action="{{ route('abstract_posts.updatestatus', $abstract_post->id) }}" method="POST">
                                @csrf
                                <div class="col-7">
                                    <label class="form-label text-muted mb-0 d-block">Comment:</label>
                                    <input type="text" class="form-control" placeholder="Comment" name="comment" required>
                                </div>
                                <div class="col-3">
                                    <label class="form-label text-muted mb-0 d-block">Status from <b>{{ $abstract_post->status }}</b> to: </label>
                                    <select class="form-select" name="status">
                                        <option value="draft" {{ $abstract_post->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="submitted" {{ $abstract_post->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                        <option value="accepted" {{ $abstract_post->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="rejected" {{ $abstract_post->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-2 pt-2">
                                    <button class="btn btn-secondary w-100 mt-3">Add</button>
                                </div>
                            </form>

                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                        @endif

                        <h6 class="fw-bold text-primary">Comments</h6>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        @foreach($abstract_post->notes as $note)
                        <p class="text-black mb-0">{{ $note->comment }}</p>
                        <small class="text-muted d-block"> {{ $note->status_change }}</small>
                        <small class="text-muted"> {{ $note->created_at->format('d/m/Y h:i A') }}</small>
                        <hr class="my-1">
                        @endforeach
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