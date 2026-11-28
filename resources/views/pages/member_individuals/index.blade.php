@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing">
                <div class="statbox widget box box-shadow member-institutions-page">
                    <div class="widget-header institution-page-header">
                        <div>
                            <h4>Individual Members</h4>
                            <p>Manage individual CUGH members and their current status.</p>
                        </div>
                        <button type="button" class="btn btn-primary institution-add-button" data-bs-toggle="modal" data-bs-target="#addIndividualModal">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                            Add member
                        </button>
                    </div>
                    <div class="widget-content widget-content-area">
                        @if(session('success'))
                            <div class="alert alert-success" role="status">{{ session('success') }}</div>
                        @endif
                        <form method="GET" action="{{ route('member_individuals.index') }}" class="institution-toolbar">
                            <div class="institution-search-group">
                                <label for="individualMemberSearch" class="visually-hidden">Search individual members</label>
                                <div class="institution-search-field">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                                    <input type="search" id="individualMemberSearch" name="search" class="form-control" value="{{ $search }}" placeholder="Search name, email or ID">
                                </div>
                                <button type="submit" class="btn btn-primary institution-search-button">Search</button>
                                @if($search !== '')
                                    <a href="{{ route('member_individuals.index') }}" class="institution-clear-link">Clear</a>
                                @endif
                            </div>
                            <span class="institution-count">{{ $members->total() }} {{ $members->total() === 1 ? 'member' : 'members' }}</span>
                        </form>

                        <div class="table-responsive institution-table-wrap">
                            <table class="table table-hover member-institutions-table individual-members-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="institution-id">ID</th>
                                        <th scope="col" class="individual-name">Member</th>
                                        <th scope="col" class="individual-email">Email</th>
                                        <th scope="col" class="individual-registration">Registration</th>
                                        <th scope="col" class="institution-status">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($members as $member)
                                        <tr>
                                            <td class="institution-id">{{ $member->id }}</td>
                                            <td class="individual-name">{{ trim($member->first_name.' '.$member->last_name) ?: '—' }}</td>
                                            <td class="individual-email"><a href="mailto:{{ $member->email }}" class="member-email">{{ $member->email }}</a></td>
                                            <td class="individual-registration">
                                                @if($member->has_registration)
                                                    <span class="member-registration-badge is-registered">Registered</span>
                                                    <span class="member-registration-badge {{ $member->used_individual_membership ? 'is-member' : 'is-not-member' }}">
                                                        {{ $member->used_individual_membership ? 'Used as individual member' : 'Not used as individual member' }}
                                                    </span>
                                                @else
                                                    <span class="member-registration-badge is-not-registered">No registration</span>
                                                @endif
                                            </td>
                                            <td class="institution-status">
                                                <span class="member-status-badge {{ $member->is_active ? 'is-active' : 'is-inactive' }}">
                                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">No individual members found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($members->hasPages())
                            <div class="mt-3">{{ $members->onEachSide(1)->links() }}</div>
                        @endif

                        <div class="modal fade" id="addIndividualModal" tabindex="-1" aria-labelledby="addIndividualModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('member_individuals.store') }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addIndividualModalLabel">Add individual member</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="memberFirstName" class="form-label">First name <span class="text-muted">(optional)</span></label>
                                                <input type="text" id="memberFirstName" name="first_name" maxlength="255" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" autocomplete="given-name">
                                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="memberLastName" class="form-label">Last name <span class="text-muted">(optional)</span></label>
                                                <input type="text" id="memberLastName" name="last_name" maxlength="255" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" autocomplete="family-name">
                                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div>
                                                <label for="memberEmail" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" id="memberEmail" name="email" maxlength="255" required class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" autocomplete="email">
                                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Add member</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
        const modal = document.getElementById('addIndividualModal');
        if (modal && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modal).show();
        }
    @endif
});
</script>
@endsection
