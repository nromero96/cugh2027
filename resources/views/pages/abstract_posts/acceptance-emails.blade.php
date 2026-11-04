@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-12">
            <div class="statbox widget box box-shadow">
                <div class="widget-header px-3 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="mb-1">Abstract Acceptance Emails</h4>
                        <p class="text-muted mb-0">Accepted Oral and Poster abstracts. Each notice is sent separately to the account that registered the abstract.</p>
                    </div>
                    <a href="{{ route('abstract_posts.index') }}" class="btn btn-outline-secondary">Back to Abstracts</a>
                </div>
                <div class="widget-content widget-content-area">
                    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                        </div>
                    @endif

                    <form method="GET" action="{{ route('abstract_posts.acceptance_emails.index') }}" class="mb-3">
                        <div class="d-flex gap-2">
                            <input type="search" name="search" value="{{ $search }}" maxlength="255" class="form-control" style="min-width:0;flex:1 1 auto;" aria-label="Search by abstract ID or account email" placeholder="Search by abstract #ID or account email...">
                            <button type="submit" class="btn btn-primary" style="flex:0 0 auto;white-space:nowrap;">Search</button>
                            @if($search !== '')
                                <a href="{{ route('abstract_posts.acceptance_emails.index') }}" class="btn btn-outline-secondary" style="flex:0 0 auto;white-space:nowrap;">Clear</a>
                            @endif
                        </div>
                    </form>

                    <p class="text-muted">@if($search !== ''){{ $abstracts->total() }} matching accepted abstracts.@else{{ $abstracts->total() }} accepted abstracts, including notices already sent.@endif Sending records the email and PDF while the abstract remains Accepted.</p>

                    @forelse($abstracts as $abstract)
                        @php
                            $recipient = $abstract->user;
                            $sentEmail = $abstract->emails->first();
                            $presenterName = $abstract->presenterName();
                            $supportedPresentation = in_array($abstract->presentation_type, ['Oral Presentation', 'Poster'], true);
                            $ready = !$sentEmail && $supportedPresentation && $recipient && filter_var($recipient->email, FILTER_VALIDATE_EMAIL)
                                && trim((string) $abstract->title) !== '';
                        @endphp
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <strong>Abstract #{{ $abstract->id }}</strong>
                                        <span class="badge badge-light-secondary">{{ $abstract->abstract_type }}</span>
                                        <span class="badge badge-light-primary">{{ $abstract->presentation_type }}</span>
                                        @if($sentEmail)
                                            <span class="badge badge-light-success">Notice sent</span>
                                        @else
                                            <span class="badge badge-light-success">Accepted</span>
                                        @endif
                                    </div>
                                    <div class="fw-semibold">{{ $abstract->title ?: 'Title missing' }}</div>
                                    <div class="small text-muted">To: {{ $sentEmail ? $sentEmail->recipient_email : ($recipient->email ?? 'no email') }}</div>
                                    @if($sentEmail)
                                        <div class="small text-success">Sent: {{ $sentEmail->sent_at ? $sentEmail->sent_at->format('Y-m-d H:i:s') : 'Date unavailable' }}</div>
                                    @endif
                                    <div class="small text-muted">Main author: {{ $presenterName ?: 'Not provided' }}</div>
                                    <div class="small text-muted">Subject: {{ $sentEmail ? $sentEmail->subject : 'NOTIFICATION OF ABSTRACT ACCEPTANCE N° '.$abstract->id.($presenterName !== '' ? ' - '.$presenterName : '') }}</div>
                                    @unless($ready || $sentEmail)
                                        <div class="small text-danger mt-1">@if(!$recipient || !filter_var($recipient->email, FILTER_VALIDATE_EMAIL))A valid account email is required.@endif @if(trim((string) $abstract->title) === '')An abstract title is required.@endif @if(!$supportedPresentation)An Oral or Poster presentation type is required.@endif</div>
                                    @endunless
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @if($sentEmail)
                                        <a href="{{ route('abstract_posts.emails.show', $sentEmail->id) }}" target="_blank" rel="noopener" class="btn btn-outline-primary">View Sent Email</a>
                                        @if($sentEmail->attachment_path)
                                            <a href="{{ route('abstract_posts.emails.attachment', $sentEmail->id) }}" class="btn btn-outline-secondary">Download PDF</a>
                                        @endif
                                    @elseif($ready)
                                        <a href="{{ route('abstract_posts.acceptance_emails.preview', $abstract->id) }}" target="_blank" rel="noopener" class="btn btn-outline-primary">Preview Email</a>
                                        <a href="{{ route('abstract_posts.acceptance_emails.letter', $abstract->id) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary">Preview PDF</a>
                                        <form action="{{ route('abstract_posts.acceptance_emails.send', $abstract->id) }}" method="POST" onsubmit="return confirm('Send the {{ $abstract->presentation_type }} acceptance email for abstract #{{ $abstract->id }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">Send Acceptance</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info mb-0">{{ $search !== '' ? 'No accepted abstracts match your search.' : 'No accepted abstracts were found.' }}</div>
                    @endforelse

                    <div class="mt-3">{{ $abstracts->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
