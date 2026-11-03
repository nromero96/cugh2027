@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-12">
            <div class="statbox widget box box-shadow">
                <div class="widget-header px-3 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="mb-1">Abstract Rejection Emails</h4>
                        <small class="text-muted mb-0">In Progress, Submitted and Qualified abstracts await a decision email. Search also finds notices already sent.</small>
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

                    <form method="GET" action="{{ route('abstract_posts.rejection_emails.index') }}" class="mb-3">
                        <div class="d-flex gap-2">
                            <input id="rejection-search" type="search" name="search" value="{{ $search }}" maxlength="255" class="form-control" style="min-width:0;flex:1 1 auto;" placeholder="Search by abstract #ID or account email...">
                            <button type="submit" class="btn btn-primary" style="flex:0 0 auto;white-space:nowrap;">Search</button>
                            @if($search !== '')
                                <a href="{{ route('abstract_posts.rejection_emails.index') }}" class="btn btn-outline-secondary" style="flex:0 0 auto;white-space:nowrap;">Clear</a>
                            @endif
                        </div>
                    </form>

                    <small class="text-muted">@if($search !== ''){{ $abstracts->total() }} matching abstracts and sent notices.@else{{ $abstracts->total() }} abstracts awaiting a rejection decision.@endif A successful send changes the status to Not Accepted and saves the email and attached letter PDF.</small>

                    @forelse($abstracts as $abstract)
                        @php
                            $recipient = $abstract->user;
                            $sentEmail = $abstract->emails->first();
                            $recipientName = $recipient ? trim($recipient->name.' '.$recipient->lastname.' '.$recipient->second_lastname) : '';
                            $recipientLabel = $recipientName !== ''
                                ? trim((in_array($recipient->salutation, ['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Prof.'], true) ? $recipient->salutation : '').' '.$recipientName)
                                : '';
                            $ready = !$sentEmail && $recipient && filter_var($recipient->email, FILTER_VALIDATE_EMAIL)
                                && trim((string) $abstract->title) !== '';
                        @endphp
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <strong>Abstract #{{ $abstract->id }}</strong>
                                        @if($sentEmail)
                                            <span class="badge badge-light-success">Notice sent</span>
                                        @else
                                            <span class="badge badge-light-secondary text-capitalize">{{ $abstract->status === 'draft' ? 'In Progress' : $abstract->status }}</span>
                                        @endif
                                    </div>
                                    <div class="fw-semibold">{{ $abstract->title ?: 'Title missing' }}</div>
                                    <div class="small text-muted">To: @if($sentEmail){{ $sentEmail->recipient_email }}@elseif($recipientLabel !== ''){{ $recipientLabel }} &lt;{{ $recipient->email }}&gt;@else{{ $recipient->email ?? 'no email' }}@endif</div>
                                    @if($sentEmail)
                                        <div class="small text-success">Sent: {{ $sentEmail->sent_at ? $sentEmail->sent_at->format('Y-m-d H:i:s') : 'Date unavailable' }}</div>
                                    @endif
                                    <div class="small text-muted">Presenter: {{ $abstract->presenterName() ?: 'Not provided' }}</div>
                                    <div class="small text-muted">Subject: NOTIFICATION OF ABSTRACT DECISION N° {{ $abstract->id }}@if($abstract->presenterName()) - {{ $abstract->presenterName() }}@endif</div>
                                    @unless($ready || $sentEmail)
                                        <div class="small text-danger mt-1">@if(!$recipient || !filter_var($recipient->email, FILTER_VALIDATE_EMAIL))A valid account email is required.@endif @if(trim((string) $abstract->title) === '')An abstract title is required.@endif</div>
                                    @endunless
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @if($sentEmail)
                                        <a href="{{ route('abstract_posts.emails.show', $sentEmail->id) }}" target="_blank" rel="noopener" class="btn btn-outline-primary">View Sent Email</a>
                                        @if($sentEmail->attachment_path)
                                            <a href="{{ route('abstract_posts.emails.attachment', $sentEmail->id) }}" class="btn btn-outline-secondary">Download PDF</a>
                                        @endif
                                    @elseif($ready)
                                        <a href="{{ route('abstract_posts.rejection_emails.preview', $abstract->id) }}" target="_blank" rel="noopener" class="btn btn-outline-primary">Preview Email</a>
                                        <a href="{{ route('abstract_posts.rejection_emails.letter', $abstract->id) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary">Preview PDF</a>
                                        <form action="{{ route('abstract_posts.rejection_emails.send', $abstract->id) }}" method="POST" onsubmit="return confirm('Send this rejection email and mark abstract #{{ $abstract->id }} as Not Accepted?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Send Rejection</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info mb-0">{{ $search !== '' ? 'No abstracts or sent notices match your search.' : 'No abstracts are awaiting rejection emails.' }}</div>
                    @endforelse

                    <div class="mt-3">{{ $abstracts->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
