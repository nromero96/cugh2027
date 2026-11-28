@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing">
                <div class="statbox widget box box-shadow member-institutions-page">
                    <div class="widget-header institution-page-header">
                        <div>
                            <h4>Member Institutions</h4>
                            <p>Manage the institutions available for CUGH membership.</p>
                        </div>
                        @if(Auth::user()->hasRole('Administrador'))
                            <button type="button" class="btn btn-primary institution-add-button" data-bs-toggle="modal" data-bs-target="#addInstitutionModal">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                                Add institution
                            </button>
                        @endif
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        @if(session('success'))
                            <div class="alert alert-success" role="status">{{ session('success') }}</div>
                        @endif
                        @if(Auth::user()->hasRole('Administrador'))
                            <form method="GET" action="{{ route('member_institutions.index') }}" class="institution-toolbar">
                                <div class="institution-search-group">
                                    <label for="institutionSearch" class="visually-hidden">Search institutions</label>
                                    <div class="institution-search-field">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                                        <input type="search" id="institutionSearch" name="search" class="form-control" value="{{ $search }}" placeholder="Search institution, country or ID">
                                    </div>
                                    <button type="submit" class="btn btn-primary institution-search-button">Search</button>
                                    @if($search !== '')
                                        <a href="{{ route('member_institutions.index') }}" class="institution-clear-link">Clear</a>
                                    @endif
                                </div>
                                <span class="institution-count">{{ $member_institutions->count() }} {{ $member_institutions->count() === 1 ? 'institution' : 'institutions' }}</span>
                            </form>
                        @endif
                        <div class="table-responsive institution-table-wrap">
                            <table class="table table-hover member-institutions-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="institution-id">ID</th>
                                        <th scope="col" class="institution-name">Institution</th>
                                        <th scope="col" class="institution-country">Country</th>
                                        <th scope="col" class="institution-registrations">Registrations</th>
                                        <th scope="col" class="institution-status">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(Auth::user()->hasRole('Administrador'))
                                        @forelse ($member_institutions as $institution)
                                            <tr>
                                                <td class="institution-id">
                                                    {{$institution->id}}
                                                </td>
                                                <td class="institution-name">
                                                    {{$institution->name}}
                                                </td>
                                                <td class="institution-country">
                                                    @if($institution->country_name)
                                                        {{ $institution->country_name }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="institution-registrations">
                                                    @if($institution->registrations_count)
                                                        <button type="button" class="registration-count registration-emails-trigger" data-url="{{ route('member_institutions.registration_emails', $institution->id) }}" aria-label="View registration emails for {{ $institution->name }}" aria-expanded="false">{{ $institution->registrations_count }}</button>
                                                    @else
                                                        <span class="registration-count is-zero">0</span>
                                                    @endif
                                                </td>
                                                <td class="institution-status">
                                                    <div class="switch form-switch-custom form-switch-primary">
                                                        <input  
                                                            class="switch-input status-switch" 
                                                            type="checkbox" 
                                                            role="switch"
                                                            aria-label="Active status for {{ $institution->name }}"
                                                            data-id="{{ $institution->id }}"
                                                            id="status_{{ $institution->id }}"
                                                            {{ $institution->is_active ? 'checked' : '' }}>
                                                        
                                                        <label class="switch-label" for="status_{{ $institution->id }}"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted py-4">No institutions found.</td></tr>
                                        @endforelse
                                    @else
                                        <tr>
                                            <td colspan="5">
                                                <h5 class="text-center">You do not have permission to access this section.</h5>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if(Auth::user()->hasRole('Administrador'))
                            <div class="modal fade" id="addInstitutionModal" tabindex="-1" aria-labelledby="addInstitutionModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('member_institutions.store') }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addInstitutionModalLabel">Add member institution</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="institutionName" class="form-label">Institution name <span class="text-danger">*</span></label>
                                                    <input type="text" id="institutionName" name="name" maxlength="255" required class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" autocomplete="organization">
                                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div>
                                                    <label for="institutionCountry" class="form-label">Country <span class="text-muted">(optional)</span></label>
                                                    <select id="institutionCountry" name="country" class="form-select @error('country') is-invalid @enderror">
                                                        <option value="">No country selected</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" {{ (string) old('country') === (string) $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Add institution</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($errors->any())
        const modal = document.getElementById('addInstitutionModal');
        if (modal && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modal).show();
        }
    @endif

    const popup = document.createElement('div');
    popup.className = 'registration-emails-popup';
    popup.setAttribute('role', 'dialog');
    popup.setAttribute('aria-label', 'Registration emails');
    popup.hidden = true;
    document.body.appendChild(popup);

    let activeButton = null;
    let pinned = false;
    let closeTimer = null;
    const emailCache = new Map();

    function positionPopup() {
        if (!activeButton || popup.hidden) return;
        const rect = activeButton.getBoundingClientRect();
        const width = Math.min(340, window.innerWidth - 24);
        popup.style.width = width + 'px';
        popup.style.left = Math.max(12, Math.min(rect.left - width / 2 + rect.width / 2, window.innerWidth - width - 12)) + 'px';
        const below = rect.bottom + 8;
        popup.style.top = (below + popup.offsetHeight <= window.innerHeight - 12
            ? below : Math.max(12, rect.top - popup.offsetHeight - 8)) + 'px';
    }

    function hidePopup() {
        if (activeButton) activeButton.setAttribute('aria-expanded', 'false');
        activeButton = null;
        pinned = false;
        popup.hidden = true;
    }

    function scheduleClose() {
        clearTimeout(closeTimer);
        closeTimer = setTimeout(function () { if (!pinned) hidePopup(); }, 180);
    }

    function renderEmails(emails) {
        popup.replaceChildren();
        const heading = document.createElement('strong');
        heading.className = 'registration-emails-heading';
        heading.textContent = emails.length + (emails.length === 1 ? ' registration email' : ' registration emails');
        popup.appendChild(heading);
        const list = document.createElement('div');
        list.className = 'registration-emails-list';
        emails.forEach(function (email) {
            const item = document.createElement('div');
            item.textContent = email || 'No email available';
            list.appendChild(item);
        });
        popup.appendChild(list);
        positionPopup();
    }

    async function showEmails(button, keepOpen) {
        clearTimeout(closeTimer);
        if (activeButton && activeButton !== button) activeButton.setAttribute('aria-expanded', 'false');
        activeButton = button;
        pinned = keepOpen;
        button.setAttribute('aria-expanded', 'true');
        popup.textContent = 'Loading emails…';
        popup.hidden = false;
        positionPopup();
        const url = button.dataset.url;
        try {
            if (!emailCache.has(url)) {
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error('Could not load emails');
                const result = await response.json();
                emailCache.set(url, result.emails);
            }
            if (activeButton === button) renderEmails(emailCache.get(url));
        } catch (error) {
            if (activeButton === button) {
                popup.textContent = 'Could not load registration emails.';
                positionPopup();
            }
        }
    }

    document.querySelectorAll('.registration-emails-trigger').forEach(function (button) {
        button.addEventListener('mouseenter', function () { showEmails(button, false); });
        button.addEventListener('mouseleave', scheduleClose);
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            if (activeButton === button && pinned) hidePopup();
            else showEmails(button, true);
        });
    });
    popup.addEventListener('mouseenter', function () { clearTimeout(closeTimer); });
    popup.addEventListener('mouseleave', scheduleClose);
    document.addEventListener('click', function (event) {
        if (!popup.contains(event.target) && !event.target.closest('.registration-emails-trigger')) hidePopup();
    });
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape') hidePopup(); });
    window.addEventListener('resize', positionPopup);
    window.addEventListener('scroll', positionPopup, true);
});

document.addEventListener('change', async function (e) {

    if (!e.target.classList.contains('status-switch')) return;

    const checkbox = e.target;
    const id = checkbox.dataset.id;
    const status = checkbox.checked ? 1 : 0;

    checkbox.disabled = true;

    try {
        const response = await fetch("{{ route('member_institutions_update_status') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                id: id,
                status: status
            })
        });

        if (!response.ok) throw new Error('Could not update institution status.');

    } catch (error) {
        checkbox.checked = !checkbox.checked;
        alert('Could not update the institution status. Please try again.');
    }

    checkbox.disabled = false;

});
</script>
@endsection
