@extends('layouts.app')


@section('content')


<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">

        <div class="row layout-spacing">
            <div class="col-lg-12 layout-top-spacing mt-4">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{__("Success")}}!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{__("Error")}}!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 mb-2 col-12">
                                <h4>
                                    {{__("Special Code details")}}
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <form class="row g-3" action="{{ route('specialcodes.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-6">
                                <label for="code" class="form-label fw-bold">{{__("Code")}}</label>
                                <input type="text" name="code" class="form-control convert_mayus" id="code" value="{{ old('code') }}">
                                <small>The code cannot be changed after saving.</small><br>
                                {!!$errors->first("code", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label fw-bold">Amount(US$)</label>
                                <input type="number" name="amount" step="0.01" min="0" class="form-control" id="amount" value="{{ old('amount') }}">
                                <small class="text-muted">This amount appears as the registration fee. Payment options depend on “Payment required?” below.</small>
                                {!!$errors->first("amount", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label fw-bold">{{__("Description")}}</label>
                                <input type="text" name="description" class="form-control" id="description" value="{{ old('description') }}">
                                {!!$errors->first("description", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="quantity" class="form-label fw-bold">Quantity</label>
                                <input type="number" name="quantity" step="1" min="1" class="form-control" id="quantity" value="{{ old('quantity') }}">
                                {!!$errors->first("quantity", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="expiration" class="form-label fw-bold">Expiration</label>
                                <input type="date" name="expiration" class="form-control" id="expiration" value="{{ old('expiration') }}">
                                {!!$errors->first("expiration", "<span class='text-danger'>:message</span>")!!}
                            </div>

                            <div class="col-md-4">
                                <label for="payment_required" class="form-label fw-bold">{{__("Payment required?")}}</label>
                                <select name="payment_required" id="payment_required" class="form-control">
                                    <option value="Si" {{ old('payment_required') == 'Si' ? 'selected' : '' }} >Yes</option>
                                    <option value="No" {{ old('payment_required') == 'No' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label fw-bold">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Activo" {{ old('status') == 'Activo' ? 'selected' : '' }} >Active</option>
                                    <option value="Inactivo" {{ old('status') == 'Inactivo' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="usage_rule" class="form-label fw-bold">Usage rule</label>
                                <select name="usage_rule" id="usage_rule" class="form-select">
                                    @foreach (['limited' => 'Up to the quantity', 'single_use' => 'One use total', 'per_email' => 'One use per email (up to the quantity)'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('usage_rule', 'limited') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('usage_rule') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="allowed_emails" class="form-label fw-bold">Authorized emails <span class="text-danger">*</span></label>
                                <textarea name="allowed_emails" id="allowed_emails" class="form-control" rows="4" placeholder="person1@example.com&#10;person2@example.com">{{ old('allowed_emails', '') }}</textarea>
                                <small class="text-muted">One email per line. Only these accounts will see and use this Special Code. For several participants, choose “One use per email” and set the total quantity.</small>
                                @error('allowed_emails') <small class="text-danger d-block">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Applicable categories</label>
                                <div class="row g-2">
                                    @foreach ($categories as $category)
                                        <div class="col-md-4"><label class="form-check"><input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}> <span class="form-check-label">{{ $category->name }}</span></label></div>
                                    @endforeach
                                </div>
                                @error('categories') <small class="text-danger">{{ $message }}</small> @enderror
                                @error('categories.*') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12 text-end">
                                <a href="{{ route('specialcodes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


@endsection
