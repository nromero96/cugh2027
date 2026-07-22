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
                                <h4 class="display-inline-block">
                                    Abstract N°: {{ $abstract_post->id }} 
                                    @if ($abstract_post->status == 'draft')
                                        <span class="badge bg-light-warning mt-2">Draft</span>
                                    @elseif ($abstract_post->status == 'submitted')
                                        <span class="badge bg-light-info mt-2">Submitted</span>
                                    @elseif ($abstract_post->status == 'rejected')
                                        <span class="badge bg-light-danger mt-2">Rejected</span>
                                    @endif

                                    @if(\Auth::user()->hasRole('Participante') && $abstract_post->status == 'draft')
                                        <a href="{{ route('abstract_posts.edit', $abstract_post->id) }}" class="btn btn-outline-info btn-link px-2 py-0">Edit Abstract</a>
                                    @endif

                                </h4>

                                

                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-6 mb-2 col-6 text-end">
                                <span class="badge bg-light-secondary mt-2">Last Update: {{ $abstract_post->updated_at }}</span>

                                @if($abstract_post->mainAuthorCountry)
                                    <span class="d-block mt-2">
                                        {{ $abstract_post->mainAuthorCountry->name }}
                                    </span>
                                @endif

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
                                    <p class="text-black">{{ $abstract_post->subtopic }}</p>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="inputName" class="form-label text-muted mb-0 d-block">Title:</label>
                                    <p class="text-black fw-bold">{{ $abstract_post->title }}</p>
                                </div>

                                

                                @php
                                    $coAuthorsData = $abstract_post->co_authors ?? [];
                                    $institutionsData = $abstract_post->institutions ?? [];

                                    // Compatibilidad con registros antiguos
                                    if (is_string($coAuthorsData)) {
                                        $coAuthorsData = json_decode($coAuthorsData, true) ?? [];
                                    }

                                    if (is_string($institutionsData)) {
                                        $institutionsData = json_decode($institutionsData, true) ?? [];
                                    }

                                    $coAuthors = collect(
                                        is_array($coAuthorsData) ? $coAuthorsData : []
                                    );

                                    // Numerar las instituciones
                                    $institutions = collect(
                                        is_array($institutionsData) ? $institutionsData : []
                                    )->values()->map(function ($institution, $index) {
                                        $institution['number'] = $index + 1;

                                        return $institution;
                                    });

                                    // Instituciones asociadas al autor principal
                                    $mainAuthorInstitutions = [];

                                    foreach ($institutions as $institution) {
                                        $institutionAuthors = $institution['coauthors'] ?? [];

                                        if (
                                            is_array($institutionAuthors) &&
                                            in_array('main_author', $institutionAuthors, true)
                                        ) {
                                            $mainAuthorInstitutions[] = $institution['number'];
                                        }
                                    }

                                    // Instituciones asociadas a cada coautor
                                    $coAuthorsMapped = $coAuthors->map(function ($coauthor) use ($institutions) {
                                        $institutionNumbers = [];

                                        foreach ($institutions as $institution) {
                                            $institutionAuthors = $institution['coauthors'] ?? [];

                                            if (
                                                isset($coauthor['id']) &&
                                                is_array($institutionAuthors) &&
                                                in_array($coauthor['id'], $institutionAuthors, true)
                                            ) {
                                                $institutionNumbers[] = $institution['number'];
                                            }
                                        }

                                        $coauthor['institutions'] = $institutionNumbers;

                                        return $coauthor;
                                    });
                                @endphp

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0 d-block">
                                        Main author:
                                    </label>

                                    <p class="text-black mb-0">
                                        {{ $abstract_post->main_author['name'] ?? '' }}
                                        {{ $abstract_post->main_author['lastname'] ?? '' }}

                                        @if(!empty($mainAuthorInstitutions))
                                            <sup>
                                                {{ implode(',', $mainAuthorInstitutions) }}
                                            </sup>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted mb-0 d-block">
                                        Co-authors:
                                    </label>

                                    @if($coAuthorsMapped->isNotEmpty())
                                        <p class="text-black mb-4">
                                            @foreach($coAuthorsMapped as $coauthor)
                                                <span>
                                                    {{ $coauthor['name'] ?? '' }}
                                                    {{ $coauthor['lastname'] ?? '' }}

                                                    @if(!empty($coauthor['institutions']))
                                                        <sup>
                                                            {{ implode(',', $coauthor['institutions']) }}
                                                        </sup>
                                                    @endif
                                                </span>

                                                @if(!$loop->last)
                                                    <br>
                                                @endif
                                            @endforeach
                                        </p>
                                    @else
                                        <p class="text-muted">
                                            No co-authors registered.
                                        </p>
                                    @endif

                                    @if($institutions->isNotEmpty())
                                        <p class="text-black fst-italic">
                                            @foreach($institutions as $institution)
                                                <span>
                                                    <sup>{{ $institution['number'] }}</sup>
                                                    {{ $institution['name'] ?? '' }}
                                                </span>

                                                @if(!$loop->last)
                                                    <br>
                                                @endif
                                            @endforeach
                                        </p>
                                    @else
                                        <p class="text-muted">
                                            No institutions registered.
                                        </p>
                                    @endif
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
                                    <label class="form-label text-muted mb-2 d-block">
                                        Keywords:
                                    </label>

                                    @php
                                        $keywords = $abstract_post->keywords ?? [];

                                        // Compatibilidad con registros antiguos
                                        if (is_string($keywords)) {
                                            $keywords = json_decode($keywords, true) ?? [];
                                        }
                                    @endphp

                                    <p class="text-black">
                                        @if(is_array($keywords) && count($keywords))
                                            @foreach($keywords as $keyword)
                                                <span class="tag">{{ $keyword }}</span>

                                                @if(!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-muted">No keywords registered.</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-12 text-end no-print">
                                    @if(\Auth::user()->hasRole('Participante') && $abstract_post->status == 'draft')
                                        <a href="{{ route('abstract_posts.edit', $abstract_post->id) }}" class="btn btn-info">Edit Abstract</a>
                                    @endif

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

                        <h6 class="fw-bold text-primary">Comments (for internal use only)</h6>
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