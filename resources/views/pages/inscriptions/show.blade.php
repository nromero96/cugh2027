@extends('layouts.app')


@section('content')

<style>
    .ficha-inscripcion .bx-text { min-height: calc(1.5em + 8px); line-height: 1.5; }
    .ficha-inscripcion .registration-confirmed-icon { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; flex: 0 0 auto; border: 1px solid #bfe8d0; border-radius: 50%; background: #eaf7ef; color: #228354; box-shadow: 0 3px 10px rgba(34, 131, 84, .1); }
    .ficha-inscripcion .registration-confirmed-icon svg { width: 20px; height: 20px; stroke: currentColor; }
    .ficha-inscripcion .btn.registration-header-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 40px;
        padding: 5px 14px 5px 6px;
        border-radius: 11px;
        font-weight: 600;
        white-space: nowrap;
        transition: background-color .2s, border-color .2s, box-shadow .2s, transform .2s;
    }
    .ficha-inscripcion .btn.registration-header-action:hover { transform: translateY(-1px); }
    .ficha-inscripcion .btn.registration-note-trigger { position: relative; overflow: visible; border: 1px solid #efc2c8; background: #fff3f4; color: #a71929; box-shadow: 0 2px 8px rgba(122, 20, 32, .08); }
    .ficha-inscripcion .btn.registration-note-trigger:hover {
        background: #f7d2d6;
        border-color: #eaa1aa;
        color: #8f1422;
        box-shadow: 0 5px 14px rgba(122, 20, 32, .14);
    }
    .registration-header-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        flex: 0 0 auto;
    }
    .ficha-inscripcion .btn.registration-note-trigger svg { width: 16px; height: 16px; stroke: currentColor; }
    .registration-note-alert {
        position: absolute;
        top: -8px;
        right: -8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 23px;
        height: 23px;
        border: 2px solid #fff;
        border-radius: 50%;
        background: #cc1f2f;
        color: #fff;
        box-shadow: 0 2px 7px rgba(122, 20, 32, .25);
        pointer-events: none;
    }
    .ficha-inscripcion .btn.registration-note-trigger .registration-note-alert svg { width: 12px; height: 12px; stroke: #fff; }
    .registration-note-trigger .registration-header-icon { background: #f7d2d6; }
    .registration-note-trigger:hover .registration-header-icon { background: #f0b8bf; }
    .ficha-inscripcion .btn.registration-edit-action { border: 1px solid #d8dfe6; background: #fff; color: #344054; box-shadow: 0 2px 8px rgba(31, 41, 55, .07); }
    .ficha-inscripcion .btn.registration-edit-action svg { width: 28px; height: 28px; padding: 6px; border-radius: 8px; background: #eef1f5; color: #344054; fill: currentColor; flex: 0 0 auto; }
    .ficha-inscripcion .btn.registration-edit-action:hover { border-color: #bfc9d5; background: #f5f7fa; color: #202938; box-shadow: 0 5px 14px rgba(31, 41, 55, .12); }
    .ficha-inscripcion .btn.registration-edit-action:hover svg { background: #e1e7ee; color: #202938; }
    .ficha-inscripcion .btn.registration-print-action { border: 1px solid #cc1f2f; background: #cc1f2f; color: #fff; box-shadow: 0 3px 10px rgba(204, 31, 47, .2); }
    .ficha-inscripcion .btn.registration-print-action svg { width: 28px; height: 28px; padding: 6px; border-radius: 8px; background: rgba(255, 255, 255, .18); stroke: #fff; flex: 0 0 auto; }
    .ficha-inscripcion .btn.registration-print-action:hover { border-color: #ab1828; background: #ab1828; color: #fff; box-shadow: 0 5px 14px rgba(204, 31, 47, .28); }
    .ficha-inscripcion .btn.registration-print-action:hover svg { stroke: #fff; background: rgba(255, 255, 255, .22); }
    .ficha-inscripcion .btn.registration-header-action:focus-visible,
    .modal .btn.registration-note-action:focus-visible {
        outline: 3px solid rgba(204, 31, 47, .25);
        outline-offset: 2px;
    }
    .modal .btn.registration-note-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 39px;
        padding: 8px 15px;
        border-radius: 10px;
        font-weight: 600;
        transition: background-color .2s, box-shadow .2s, transform .2s;
    }
    .modal .btn.registration-note-action svg { width: 16px; height: 16px; stroke: currentColor; }
    .modal .btn.registration-note-action:hover { transform: translateY(-1px); }
    .modal .btn.registration-note-close { border: 1px solid #dce0e5; background: #fff; color: #4b5563; }
    .modal .btn.registration-note-close:hover { border-color: #c9cfd6; background: #f3f5f7; color: #27303b; }
    .modal .btn.registration-note-save { border: 1px solid #cc1f2f; background: #cc1f2f; color: #fff; box-shadow: 0 3px 10px rgba(204, 31, 47, .2); }
    .modal .btn.registration-note-save:hover { border-color: #ab1828; background: #ab1828; color: #fff; box-shadow: 0 5px 14px rgba(204, 31, 47, .28); }
    #privateNoteModal .modal-content {
        background: #fff;
        color: #27303b;
        border: 1px solid #e6e8ec;
        border-radius: 16px;
        box-shadow: 0 18px 50px rgba(14, 23, 38, .2);
        overflow: hidden;
    }
    #privateNoteModal .modal-header,
    #privateNoteModal .modal-footer { border-color: #e9ebef; }
    #privateNoteModal .modal-title,
    #privateNoteModal .form-label { color: #27303b; }
    #privateNoteModal .form-text { color: #687384; }
    #privateNoteModal .form-control { background: #fff; color: #27303b; border-color: #cfd5dd; }
    #privateNoteModal .form-control::placeholder { color: #85909f; }
    #privateNoteModal .form-control:focus { background: #fff; color: #27303b; border-color: #cc1f2f; box-shadow: 0 0 0 3px rgba(204, 31, 47, .12); }
    #privateNoteModal .registration-modal-dismiss {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 9px;
        background: #f2f4f6;
        color: #4b5563;
    }
    #privateNoteModal .registration-modal-dismiss:hover { background: #f7d2d6; color: #8f1422; }
    #privateNoteModal .registration-modal-dismiss svg { width: 16px; height: 16px; stroke: currentColor; }
    #registrationAnswersAccordion .accordion-item { overflow: hidden; border: 1px solid #e6e8ec; border-radius: 12px; background: #fff; }
    #registrationAnswersAccordion .accordion-button { display: flex; align-items: center; gap: 12px; padding: 15px 18px; background: #fff4f5; color: #27303b; box-shadow: none; }
    #registrationAnswersAccordion .accordion-button:hover { background: #f7d2d6; }
    #registrationAnswersAccordion .accordion-button:focus-visible { outline: 3px solid rgba(204, 31, 47, .25); outline-offset: -3px; }
    #registrationAnswersAccordion .accordion-button::after { display: none; }
    #registrationAnswersAccordion .registration-answers-icon { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; background: #f7d2d6; color: #a71929; flex: 0 0 auto; }
    #registrationAnswersAccordion .registration-answers-title { display: block; color: #27303b; font-size: 15px; font-weight: 700; }
    #registrationAnswersAccordion .registration-answers-subtitle { display: block; color: #687384; font-size: 12px; font-weight: 400; }
    #info_invoice.invoice-file-card { border: 1px solid #e6e8ec; border-radius: 14px; background: #fff; box-shadow: 0 4px 18px rgba(26, 35, 51, .06); overflow: hidden; }
    #info_invoice .invoice-card-heading { display: flex; align-items: center; gap: 11px; padding: 16px 18px; border-bottom: 1px solid #eef0f3; }
    #info_invoice .invoice-card-icon { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; flex: 0 0 auto; border-radius: 10px; background: #fff0f2; color: #bd1c2c; }
    #info_invoice .invoice-card-title { margin: 0; color: #27303b; font-size: 16px; font-weight: 700; }
    #info_invoice .invoice-card-subtitle { display: block; margin-top: 2px; color: #687384; font-size: 12px; }
    #info_invoice .invoice-card-body { display: flex; align-items: center; gap: 12px; padding: 16px 18px; }
    #info_invoice .invoice-file-name { min-width: 0; flex: 1; overflow-wrap: anywhere; color: #344054; font-size: 13px; font-weight: 600; line-height: 1.4; }
    #info_invoice .invoice-card-actions { display: flex; align-items: center; gap: 8px; flex: 0 0 auto; }
    #info_invoice .invoice-card-actions form { margin: 0; }
    #info_invoice .invoice-card-action { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; padding: 0; border-radius: 9px; transition: background-color .2s, border-color .2s, color .2s; }
    #info_invoice .invoice-card-action svg { width: 18px; height: 18px; stroke: currentColor; }
    #info_invoice .invoice-download { border: 1px solid #efc2c8; background: #fff3f4; color: #a71929; }
    #info_invoice .invoice-download:hover { border-color: #eaa1aa; background: #f7d2d6; color: #8f1422; }
    #info_invoice .invoice-delete { border: 1px solid #e3e6ea; background: #fff; color: #687384; }
    #info_invoice .invoice-delete:hover { border-color: #efc2c8; background: #fff3f4; color: #a71929; }
    #info_invoice .invoice-card-action:focus-visible { outline: 3px solid rgba(204, 31, 47, .25); outline-offset: 2px; }
    #form_upload_invoice.invoice-upload-card { border: 1px solid #e6e8ec; border-radius: 14px; background: #fff; box-shadow: 0 4px 18px rgba(26, 35, 51, .06); overflow: hidden; }
    #form_upload_invoice .invoice-upload-heading { display: flex; align-items: center; gap: 11px; padding: 16px 18px; border-bottom: 1px solid #eef0f3; }
    #form_upload_invoice .invoice-upload-icon { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; flex: 0 0 auto; border-radius: 10px; background: #fff0f2; color: #bd1c2c; }
    #form_upload_invoice .invoice-upload-title { margin: 0; color: #27303b; font-size: 16px; font-weight: 700; }
    #form_upload_invoice .invoice-upload-subtitle { display: block; margin-top: 2px; color: #687384; font-size: 12px; }
    #form_upload_invoice .invoice-upload-body { padding: 16px 18px 18px; }
    #form_upload_invoice .invoice-upload-label { display: block; margin-bottom: 8px; color: #344054; font-size: 13px; font-weight: 600; }
    #form_upload_invoice .form-control { border-color: #d8dfe6; border-radius: 9px; color: #344054; }
    #form_upload_invoice .form-control::file-selector-button { border-color: #eef0f3; background: #f6f7f9; color: #344054; }
    #form_upload_invoice .form-control:hover::file-selector-button { background: #fff0f2; color: #a71929; }
    #form_upload_invoice .form-control:focus { border-color: #cc1f2f; box-shadow: 0 0 0 3px rgba(204, 31, 47, .12); }
    #form_upload_invoice .invoice-upload-hint { display: block; margin-top: 7px; color: #687384; font-size: 12px; }
    #form_upload_invoice .invoice-upload-submit { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 40px; margin-top: 16px; border: 1px solid #cc1f2f; border-radius: 9px; background: #cc1f2f; color: #fff; font-weight: 600; transition: background-color .2s, border-color .2s, box-shadow .2s; }
    #form_upload_invoice .invoice-upload-submit:hover { border-color: #ab1828; background: #ab1828; color: #fff; box-shadow: 0 4px 12px rgba(204, 31, 47, .2); }
    #form_upload_invoice .invoice-upload-submit:focus-visible { outline: 3px solid rgba(204, 31, 47, .25); outline-offset: 2px; }
    #form_upload_invoice .invoice-upload-submit svg { width: 17px; height: 17px; stroke: currentColor; }
    .actionstatus.registration-status-card { border: 1px solid #b51b2a; border-radius: 14px; background: #cc1f2f; color: #fff; box-shadow: 0 6px 20px rgba(146, 21, 35, .2); overflow: hidden; }
    .actionstatus .registration-status-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 18px; border-bottom: 1px solid rgba(255, 255, 255, .22); background: rgba(111, 8, 21, .12); }
    .actionstatus .registration-status-heading-main { display: flex; align-items: center; gap: 11px; min-width: 0; }
    .actionstatus .registration-status-icon { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; flex: 0 0 auto; border-radius: 10px; background: rgba(255, 255, 255, .18); color: #fff; }
    .actionstatus .registration-status-title { margin: 0; color: #fff; font-size: 16px; font-weight: 700; }
    .actionstatus .registration-status-subtitle { display: block; margin-top: 2px; color: #ffe0e4; font-size: 12px; }
    .actionstatus .registration-current-status { padding: 6px 10px; border: 1px solid #fff; border-radius: 999px; background: #fff; color: #a71929; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .actionstatus .registration-status-body { padding: 16px 18px 18px; }
    .actionstatus .registration-status-label { display: block; margin-bottom: 7px; color: #fff; font-size: 13px; font-weight: 600; }
    .actionstatus .form-control { min-height: 40px; border-color: #d8dfe6; border-radius: 9px; background: #fff; color: #344054; }
    .actionstatus .form-control:focus { border-color: #fff; box-shadow: 0 0 0 3px rgba(255, 255, 255, .28); }
    .actionstatus .registration-status-save { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 40px; border: 1px solid #fff; border-radius: 9px; background: #fff; color: #a71929; font-weight: 600; white-space: nowrap; }
    .actionstatus .registration-status-save:hover { border-color: #f7d2d6; background: #f7d2d6; color: #8f1422; box-shadow: 0 4px 12px rgba(74, 5, 14, .2); }
    .actionstatus .registration-status-save:focus-visible { outline: 3px solid rgba(255, 255, 255, .7); outline-offset: 2px; }
    .actionstatus .registration-status-save svg { width: 17px; height: 17px; stroke: currentColor; }
    @media (max-width: 575.98px) { .actionstatus .registration-status-heading { align-items: flex-start; flex-direction: column; } .actionstatus .registration-current-status { margin-left: 49px; } }
    #registrationStatusHistory { border: 1px solid #e6e8ec; border-radius: 14px; background: #fff; box-shadow: 0 4px 18px rgba(26, 35, 51, .06); overflow: hidden; }
    #registrationStatusHistory .status-history-heading { display: flex; align-items: center; gap: 11px; padding: 16px 18px; border-bottom: 1px solid #eef0f3; }
    #registrationStatusHistory .status-history-icon { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; flex: 0 0 auto; border-radius: 10px; background: #fff0f2; color: #bd1c2c; }
    #registrationStatusHistory .status-history-title { margin: 0; color: #27303b; font-size: 16px; font-weight: 700; }
    #registrationStatusHistory .status-history-count { margin-left: auto; padding: 5px 9px; border-radius: 999px; background: #f2f4f7; color: #667085; font-size: 12px; font-weight: 700; }
    #registrationStatusHistory .status-history-list { margin: 0; padding: 16px 18px 18px 18px; list-style: none; }
    #registrationStatusHistory .status-history-entry { position: relative; margin-left: 10px; padding: 0 0 17px 25px; border-left: 2px solid #e9edf1; }
    #registrationStatusHistory .status-history-entry:last-child { padding-bottom: 0; border-left-color: transparent; }
    #registrationStatusHistory .status-history-entry::before { position: absolute; top: 4px; left: -6px; width: 10px; height: 10px; border: 2px solid #fff; border-radius: 50%; background: #cc1f2f; box-shadow: 0 0 0 2px #f7d2d6; content: ''; }
    #registrationStatusHistory .status-history-action { display: block; color: #344054; font-size: 13px; font-weight: 700; line-height: 1.4; }
    #registrationStatusHistory .status-history-date { display: block; margin-top: 3px; color: #687384; font-size: 12px; }
    #registrationStatusHistory .status-history-note { margin: 9px 0 0; padding: 9px 11px; border-radius: 8px; background: #f7f8fa; color: #4b5563; font-size: 13px; line-height: 1.45; overflow-wrap: anywhere; white-space: pre-wrap; }
    #registrationStatusHistory .status-history-empty { padding: 20px 18px; color: #687384; font-size: 13px; }
    #registrationStatusHistory .status-history-extra-list { padding-top: 0; }
    #registrationStatusHistory .status-history-toggle { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 11px 18px; border: 0; border-top: 1px solid #eef0f3; background: #fff; color: #a71929; font-size: 13px; font-weight: 700; transition: background-color .2s; }
    #registrationStatusHistory .status-history-toggle:hover { background: #fff3f4; color: #8f1422; }
    #registrationStatusHistory .status-history-toggle:focus-visible { outline: 3px solid rgba(204, 31, 47, .25); outline-offset: -3px; }
    #registrationStatusHistory .status-history-toggle-icon { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: #f7d2d6; font-size: 16px; line-height: 1; }
    #registrationStatusHistory .status-history-toggle[aria-expanded="false"] .status-history-hide,
    #registrationStatusHistory .status-history-toggle[aria-expanded="true"] .status-history-show { display: none; }
    #registrationAnswersAccordion .registration-answers-chevron { margin-left: auto; color: #a71929; transition: transform .2s; }
    #registrationAnswersAccordion .accordion-button:not(.collapsed) .registration-answers-chevron { transform: rotate(180deg); }
    #registrationAnswersAccordion .accordion-body { background: #fff; color: #27303b; }
    #registrationAnswersAccordion .form-check-label { color: #344054; }
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

                <div class="statbox widget box box-shadow ficha-inscripcion">
                    <div class="widget-header px-3">
                        <div class="row g-3">
                            <div class="col-md-7 py-3">
                                <h4 class="px-0 py-0">
                                    {{__("Registration")}} # {{ $inscription->id }}
                                </h4>
                            </div>
                            <div class="col-md-5 py-3 text-end">
                                <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mb-2">

                                @if($inscription->status === 'Confirmed')
                                    <span class="registration-confirmed-icon" role="img" aria-label="Registration confirmed" title="Registration confirmed">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                                    </span>
                                @endif

                                @if(\Auth::user()->hasRole('Administrador'))
                                    <button type="button" class="btn registration-header-action registration-note-trigger" data-bs-toggle="modal" data-bs-target="#privateNoteModal">
                                        <span class="registration-header-icon">
                                            @if($inscription->private_note)
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M12 7v10M7 12h10"/></svg>
                                            @endif
                                        </span>
                                        {{ $inscription->private_note ? 'View Note' : 'Add Note' }}
                                        @if($inscription->private_note)
                                            <span class="registration-note-alert" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" focusable="false"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9z"/><path d="M10 21h4"/></svg>
                                            </span>
                                        @endif
                                    </button>
                                @endif

                                @if(\Auth::user()->hasRole('Administrador') && $inscription->status !== 'Confirmed')
                                    <a href="{{ route('inscriptions.edit', $inscription->id) }}" class="btn registration-header-action registration-edit-action btneditinsc"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg> Edit</a>
                                @endif

                                <a href="#" class="btn registration-header-action registration-print-action btnprintficha" aria-label="Print registration">
                                    <svg width="14" height="14" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                        <path d="M6 9V2h12v7"></path>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                        <path d="M6 14h12v8H6z"></path>
                                    </svg> Print
                                </a>
                                </div>

                                @if($inscription->status == 'Pagado')
                                    <span class="badge badge-light-success">{{ $inscription->status .' ('.$inscription->payment_method.')' }}</span>
                                @elseif ($inscription->status == 'Procesando')
                                    <span class="badge badge-light-info">{{ $inscription->status .' ('.$inscription->payment_method.')' }}</span>
                                @elseif ($inscription->status == 'Pending Payment')
                                    <span class="badge badge-light-warning">{{ $inscription->status .' ('.$inscription->payment_method.')' }}</span>
                                @elseif ($inscription->status == 'Rechazado')
                                    <span class="badge badge-light-danger">{{ $inscription->status .' ('.$inscription->payment_method.')' }}</span>
                                @endif
                                <span class="d-block">{{ $inscription->created_at }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area pt-0">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Salutation:</label><br>
                                <span class="bx-text">{{ $user->salutation }}</span>
                            </div>
                            <div class="col-md-8">
                                
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">First Name:</label><br>
                                <span class="bx-text">{{ $user->name }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Middle Name:</label><br>
                                <span class="bx-text">{{ $user->lastname }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Last Name:</label><br>
                                <span class="bx-text">{{ $user->second_lastname }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Degrees:</label><br>
                                <span class="bx-text">{{ $user->degrees }}</span>
                            </div>

                            <div class="col-md-4 @if($user->degrees != 'Other') d-none @else @endif" id="degrees_other">
                                <label class="form-label fw-bold mb-0">Other Degree:</label><br>
                                <span class="bx-text">{{ $user->other_degrees }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">CUGH Member:</label><br>
                                <span class="bx-text">{{ $user->is_cugh_member == 1 ? 'Yes' : 'No' }}</span>
                            </div>

                            <div class="col-md-4 @if($user->is_cugh_member != 1) d-none @else @endif" id="cugh_membership_type">
                                <label class="form-label fw-bold mb-0">CUGH Membership Type:</label><br>
                                <span class="bx-text">{{ $user->cugh_membership_type}}</span>
                            </div>

                            <div class="col-md-4 @if($user->cugh_membership_type != 'Institutional Member') d-none @else @endif" id="cugh_member_institution">
                                <label class="form-label fw-bold mb-0">CUGH Member Institution:</label><br>
                                <span class="bx-text">{{ $user->cugh_member_institution_name }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Job Title:</label><br>
                                <span class="bx-text">{{ $user->job_title }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Document Type:</label><br>
                                <span class="bx-text">{{ $user->document_type }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Document Number:</label><br>
                                <span class="bx-text">{{ $user->document_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Nationality:</label><br>
                                <span class="bx-text">{{ $user->user_nationality }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Gender:</label><br>
                                <span class="bx-text">{{ $user->gender }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Occupation:</label><br>
                                <span class="bx-text">{{ $user->occupation }}</span>
                            </div>

                            <div class="col-md-4 @if($user->occupation != 'Other') d-none @else @endif" id="occupation_other">
                                <label class="form-label fw-bold mb-0">Other Occupation:</label><br>
                                <span class="bx-text">{{ $user->occupation_other }}</span>
                            </div>

                            <div class="col-md-12">
                                    <hr class="mt-1 mb-0">
                                    <div class="row">
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">Workplace Name:</label><br>
                                            <span class="bx-text">{{ $user->workplace }}</span>
                                        </div>
                                        <div class="col-md-8 mt-3">
                                            <label class="form-label fw-bold mb-0">Workplace Postal Address:</label><br>
                                            <span class="bx-text">{{ $user->address }}</span>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">City:</label><br>
                                            <span class="bx-text">{{ $user->city }}</span>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">State:</label><br>
                                            <span class="bx-text">{{ $user->state }}</span>
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold mb-0">Country:</label><br>
                                            <span class="bx-text">{{ $user->user_country }}</span>
                                        </div>
                                    </div>
                                    <hr class="mt-3 mb-1">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Work Phone:</label><br>
                                <span class="bx-text">{{ $user->work_phone_code.' '.$user->work_phone_code_city.' '.$user->work_phone_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Cell Phone:</label><br>
                                <span class="bx-text">{{ $user->phone_code.' '.$user->phone_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">WhatsApp:</label><br>
                                <span class="bx-text">{{ $user->whatsapp_code.' '.$user->whatsapp_number }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">E-mail:</label><br>
                                <span class="bx-text">{{ $user->email }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Cc E-mail:</label><br>
                                <span class="bx-text">{{ $user->cc_email }}</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-0">Conference badge:</label><br>
                                <span class="bx-text">
                                    {{ $user->solapin_name }} | {{ $user->solapin_lastname }}
                                    <a href="{{ route('gafetes.gafeteforparticipant', $inscription->id) }}" class="float-end px-1 py-0" target="_blank">
                                        <svg width="17" height="17" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <path d="M12 9a3 3 0 1 0 0 6 3 3 0 1 0 0-6z"></path>
                                        </svg>
                                    </a>
                                </span>
                            </div>

                            <div class="col-md-12">
                                <hr class="my-0">
                            </div>

                            <div class="col-md-12">
                                <h6>
                                    Registration details
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col"><b>Descripción</b></th>
                                                <th scope="col" width="105px"><b>Information</b></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                    <td>
                                                        Category
                                                    </td>
                                                    <td>
                                                        <b>{{ $inscription->category_inscription_name }}</b>
                                                        @if($inscription->special_code != '')
                                                            <br><small class="text-info" style="font-size: 11px;">{{ $inscription->special_code }}</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Registration fee
                                                    </td>
                                                    <td>
                                                        <b>US$ {{ $inscription->price_category }}</b>
                                                    </td>
                                                </tr>
                                            <tr class="table-secondary">
                                                <td><b>Total</b></td>
                                                <td><b>US$ {{ $inscription->total }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                @if ($inscription->document_file != null || $inscription->document_file != '')
                                    <div id="dv_document_file">
                                        <label class="form-label mt-3">
                                        <span class="fw-bold">Proof document of category. ({{ $inscription->category_inscription_name }}):</span></label><br>
                                        <div class="mt-1">
                                            <a href="{{ asset('storage/uploads/document_file').'/'.$inscription->document_file}}" class="badge badge-light-primary text-start me-2 bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-bs-original-title="Download" target="_blank">
                                                {{ $inscription->document_file }}
                                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7 10 5 5 5-5"></path><path d="M12 15V3"></path></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endif

                            </div>

                            @if($inscription->invoice == 'yes')
                            <div class="col-md-12">
                                <div class="card px-3 py-3">
                                    <label for="" class="form-label fw-bold">
                                        Billing information:
                                    </label>

                                    @if($inscription->user_country == 'Peru')
                                        <label for="" class="form-label fw-bold">
                                            {{$inscription->invoice_type}}
                                        </label>
                                    @endif

                                    @php 
                                        if($inscription->invoice_type == 'Factura'){
                                            $txt_invoice_social_reason = 'Entity Name';
                                            $txt_invoice_address = 'Business Address';
                                        }else{
                                            $txt_invoice_social_reason = 'Full Name or entity';
                                            $txt_invoice_address = 'Full Postal Address';
                                        }
                                    @endphp

                                    <div class="row mt-1" id="dv_invoice_info">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold mb-0">{{ $txt_invoice_social_reason }}:</label><br>
                                            <span class="bx-text">{{ $inscription->invoice_social_reason }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold mb-0">Document Type and Number:</label><br>
                                            <span class="bx-text">{{ $inscription->invoice_type_document }} - {{ $inscription->invoice_ruc }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold mb-0">{{ $txt_invoice_address }}:</label><br>
                                            <span class="bx-text">{{ $inscription->invoice_address }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @endif

                            <div class="col-md-12">
                                <div class="card px-3 py-3">
                                    <label for="" class="form-label fw-bold mb-0">Payment Method:</label>
                                    @php 
                                        if($inscription->payment_method == 'none'){
                                            $payment_method = 'No payment required';
                                        }else{
                                            $payment_method = $inscription->payment_method;
                                        }
                                    @endphp

                                    @if($inscription->payment_method != null)
                                    <div class="">
                                        <span class="bx-text">{{ $payment_method }}</span>
                                    </div>
                                    @endif

                                    @if ($inscription->payment_method == 'Bank Transfer/Wire')
                                        <div class="row mt-1">
                                            <div class="col-md-12">
                                                <div class="mt-1">
                                                    @if($inscription->voucher_file != null)
                                                    <a href="{{ asset('storage/uploads/voucher_file').'/'.$inscription->voucher_file}}" class="badge badge-light-primary text-start me-2 bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-bs-original-title="Descargar" target="_blank">
                                                        {{ $inscription->voucher_file }}
                                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m7 10 5 5 5-5"></path><path d="M12 15V3"></path></svg>
                                                    </a>
                                                    @else
                                                        <span class="bx-text">No voucher uploaded</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($inscription->payment_method == 'Credit/Debit Card' && $inscription->status == 'Pending')
                                        <div class="row mt-1">
                                            <div class="col-12">
                                                <label class="form-label fw-bold mb-0">Pending Payment:</label><br>
                                                <a href="{{ url(config('services.upch.url_send_data') . '/' . $inscription->token) }}" class="btn btn-primary mt-2">Go to Pay</a>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($inscription->payment_method == 'Credit/Debit Card' && $paymentcards->count() > 0)
                                        @foreach ($paymentcards as $paymentcard)
                                            <div class="card px-3 py-3 mt-3" @if($paymentcard->status_payment == 'AUTORIZADO') style="background-color: #00ab5545;" @else style="background-color: #cc1f2f14;" @endif>
                                                <div class="row mt-1">
                                                    <div class="col-3">
                                                        <label class="form-label fw-bold mb-0"># Transaction number:</label><br>
                                                        <span class="bx-text">{{ $paymentcard->purchasenumber }}</span>
                                                    </div>
                                                    <div class="col-3">
                                                        <label class="form-label fw-bold mb-0">Card #:</label><br>
                                                        <span class="bx-text">{{ $paymentcard->card_number }}</span>
                                                    </div>
                                                    <div class="col-2">
                                                        <label class="form-label fw-bold mb-0">Amount:</label><br>
                                                        <span class="bx-text">{{ $paymentcard->amount.' '.$paymentcard->currency }}</span>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label fw-bold mb-0">Transaction Date:</label><br>
                                                        <span class="bx-text">{{$paymentcard->transaction_date}}</span>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <span class="bx-text">{{ $paymentcard->action_description }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if($inscription->payment_method == null)
                                        <div class="row mt-1">
                                            <div class="col-12">
                                                <span class="bx-text">No payment method selected</span>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>


                            <div class="col-md-7">

                                @if (\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                                    <div class="card registration-status-card mb-2 actionstatus">
                                        <div class="registration-status-heading">
                                            <div class="registration-status-heading-main">
                                                <span class="registration-status-icon" aria-hidden="true">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/></svg>
                                                </span>
                                                <div>
                                                    <h6 class="registration-status-title">Registration status</h6>
                                                    <span class="registration-status-subtitle">Update this registration's progress</span>
                                                </div>
                                            </div>
                                            <span class="registration-current-status">{{ $inscription->status }}</span>
                                        </div>
                                        <form class="registration-status-body" action="{{ route('inscriptions.updatestatus', ['id' => $inscription->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-3 align-items-end">
                                            <div class="col-sm-4">
                                                <label for="action" class="registration-status-label">New status</label>
                                                <select name="action" id="action" class="form-control">
                                                    <option value="Draft" @if ($inscription->status == 'Draft') selected @endif >{{ __('Draft') }}</option>
                                                    <option value="Pending" @if ($inscription->status == 'Pending') selected @endif >{{ __('Pending') }}</option>
                                                    <option value="Processing" @if ($inscription->status == 'Processing') selected @endif>{{ __('Processing') }}</option>
                                                    <option value="Paid" @if ($inscription->status == 'Paid') selected @endif>{{ __('Paid') }}</option>
                                                    <option value="Refused" @if ($inscription->status == 'Refused') selected @endif>{{ __('Refused') }}</option>
                                                    <option value="Confirmed" @if ($inscription->status == 'Confirmed') selected @endif>{{ __('Confirmed') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-5">
                                                <label for="note" class="registration-status-label">Note</label>
                                                <input type="text" class="form-control" name="note" id="note" placeholder="Add a note (optional)">
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="submit" class="btn registration-status-save w-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                                                    Update
                                                </button>
                                            </div>
                                            </div>
                                        </form>
                                    </div>

                                    @if($inscription->status == 'Paid')
                                    <form class="row confirm-form" action="{{ route('inscriptions.updatestatus', ['id' => $inscription->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-md-12">
                                            <input type="hidden" name="note" value="Confirmed">
                                            <input type="hidden" name="action" value="Confirmed">
                                            <button type="submit" class="btn btn-success w-100 mb-2 submit-btn" value="Confirmed">{{ __('Confirmed') }}</button>
                                        </div>
                                    </form>
                                    @endif

                                @endif

                                <div class="card" id="registrationStatusHistory">
                                    <div class="status-history-heading">
                                        <span class="status-history-icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7M3 4v5h5M12 7v5l3 2"/></svg>
                                        </span>
                                        <h6 class="status-history-title">Status history</h6>
                                        <span class="status-history-count">{{ $statusnotes->count() }}</span>
                                    </div>
                                    @if($statusnotes->isNotEmpty())
                                        <ol class="status-history-list">
                                            <li class="status-history-entry">
                                                <span class="status-history-action">{{ $statusnotes->first()->action }}</span>
                                                <time class="status-history-date" datetime="{{ $statusnotes->first()->created_at ? $statusnotes->first()->created_at->toIso8601String() : '' }}">{{ $statusnotes->first()->created_at ? $statusnotes->first()->created_at->format('Y-m-d H:i') : '—' }}</time>
                                                @if($statusnotes->first()->note)
                                                    <p class="status-history-note">{{ $statusnotes->first()->note }}</p>
                                                @endif
                                            </li>
                                        </ol>
                                        @if($statusnotes->count() > 1)
                                            <div class="collapse" id="olderStatusHistory">
                                                <ol class="status-history-list status-history-extra-list">
                                                    @foreach ($statusnotes->skip(1) as $item)
                                                        <li class="status-history-entry">
                                                            <span class="status-history-action">{{ $item->action }}</span>
                                                            <time class="status-history-date" datetime="{{ $item->created_at ? $item->created_at->toIso8601String() : '' }}">{{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : '—' }}</time>
                                                            @if($item->note)
                                                                <p class="status-history-note">{{ $item->note }}</p>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                            <button type="button" class="status-history-toggle" data-bs-toggle="collapse" data-bs-target="#olderStatusHistory" aria-expanded="false" aria-controls="olderStatusHistory">
                                                <span class="status-history-toggle-icon status-history-show" aria-hidden="true">+</span>
                                                <span class="status-history-show">Show {{ $statusnotes->count() - 1 }} older {{ $statusnotes->count() === 2 ? 'change' : 'changes' }}</span>
                                                <span class="status-history-toggle-icon status-history-hide" aria-hidden="true">−</span>
                                                <span class="status-history-hide">Hide older changes</span>
                                            </button>
                                        @endif
                                    @else
                                        <div class="status-history-empty">No status changes have been recorded yet.</div>
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-5">
                                <div class="card invoice-file-card {{ $inscription->compr_pdf ? '' : 'd-none' }}" id="info_invoice">
                                    <div class="invoice-card-heading">
                                        <span class="invoice-card-icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h6"/></svg>
                                        </span>
                                        <div>
                                            <h6 class="invoice-card-title">Invoice</h6>
                                            <span class="invoice-card-subtitle">Invoice document</span>
                                        </div>
                                    </div>
                                    <div class="invoice-card-body">
                                        <span class="invoice-file-name" title="{{ $inscription->compr_pdf }}">{{ $inscription->compr_pdf }}</span>
                                        <div class="invoice-card-actions">
                                            <a href="{{ asset('storage/uploads/invoices/'.$inscription->compr_pdf) }}" target="_blank" rel="noopener noreferrer" class="invoice-card-action invoice-download" title="View invoice" aria-label="View invoice">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg>
                                            </a>
                                        @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                                            <form action="{{ route('inscriptions.deleteinvoice', ['id' => $inscription->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="invoice-card-action invoice-delete" title="Delete invoice" aria-label="Delete invoice">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 13a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1l1-13M9 7V4a1 1 0 0 1 1-1h2"/><path d="M12 3h3a1 1 0 0 1 1 1v3"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if(\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria'))
                                <div class="card invoice-upload-card {{ $inscription->compr_pdf ? 'd-none' : '' }}" id="form_upload_invoice">
                                    <div class="invoice-upload-heading">
                                        <span class="invoice-upload-icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M12 18v-7m0 0-3 3m3-3 3 3"/></svg>
                                        </span>
                                        <div>
                                            <h6 class="invoice-upload-title">Upload invoice</h6>
                                            <span class="invoice-upload-subtitle">Attach the participant's invoice</span>
                                        </div>
                                    </div>
                                    <form class="invoice-upload-body" action="{{ route('inscriptions.uploadinvoice', $inscription->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <label class="invoice-upload-label" for="compr_pdf">Invoice PDF</label>
                                        <input type="file" class="form-control @error('compr_pdf') is-invalid @enderror" name="compr_pdf" id="compr_pdf" accept=".pdf,application/pdf" aria-describedby="invoice-upload-hint" required>
                                        <small class="invoice-upload-hint" id="invoice-upload-hint">PDF file, up to 2 MB.</small>
                                        @error('compr_pdf')
                                            <span class="text-danger d-block mt-2" role="alert">{{ $message }}</span>
                                        @enderror
                                        <button type="submit" class="btn invoice-upload-submit w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4m0 0-4 4m4-4 4 4M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg>
                                            Upload invoice
                                        </button>
                                    </form>
                                </div>
                                @endif


                                
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="accordion" id="registrationAnswersAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="registrationAnswersHeading">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#registrationAnswersPanel" aria-expanded="false" aria-controls="registrationAnswersPanel">
                                                <span class="registration-answers-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 5h16M4 12h16M4 19h10"/><path d="m17 18 2 2 3-4"/></svg></span>
                                                <span class="text-start"><span class="registration-answers-title">Additional registration questions</span><span class="registration-answers-subtitle">View the participant's submitted answers</span></span>
                                                <svg class="registration-answers-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                        </h2>
                                        <div id="registrationAnswersPanel" class="accordion-collapse collapse" aria-labelledby="registrationAnswersHeading" data-bs-parent="#registrationAnswersAccordion">
                                            <div class="accordion-body disabled-style">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="sector" class="form-label text-muted mb-0 mt-2">SECTOR <span class="text-danger">*</span> <small>(Check all that apply)</small></label><br>
                                                <div class="row">
                                                    @php
                                                        $selectedSectors = old('sector', $user->sector ?? []);
                                                    @endphp
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Academic" {{ in_array('Academic', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Academic</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Funding Organization" {{ in_array('Funding Organization', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Funding Organization</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Government" {{ in_array('Government', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Government</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Intergovernmental organization" {{ in_array('Intergovernmental organization', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Intergovernmental organization</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Non-profit/NGO/Civil Society Organization" {{ in_array('Non-profit/NGO/Civil Society Organization', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Non-profit/NGO/Civil Society Organization</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Private Sector" {{ in_array('Private Sector', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Private Sector</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Research Institute" {{ in_array('Research Institute', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Research Institute</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Think Tank" {{ in_array('Think Tank', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Think Tank</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="sector[]" value="Other" {{ in_array('Other', $selectedSectors) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_sector" class="form-control mb-2" id="other_sector" value="{{ old('other_sector', $user->other_sector) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("sector", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="area_of_work" class="form-label text-muted mb-0 mt-2">AREA(S) OF WORK <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                
                                                @php
                                                    $selectedAreaofworks = old('area_of_work', $user->area_of_work ?? []);
                                                @endphp
                                                
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Academic Administration" {{ in_array('Academic Administration', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Academic Administration</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Faculty" {{ in_array('Faculty', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Faculty</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Student Undergraduate" {{ in_array('Student Undergraduate', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Student Undergraduate</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Postgraduate Student" {{ in_array('Postgraduate Student', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Postgraduate Student</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Advocacy" {{ in_array('Advocacy', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Advocacy</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Research" {{ in_array('Research', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Research</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Education" {{ in_array('Education', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Education</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Funding" {{ in_array('Funding', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Funding</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Implementation" {{ in_array('Implementation', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Implementation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Politics/Policy making" {{ in_array('Politics/Policy making', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Politics/Policy making</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="area_of_work[]" value="Other" {{ in_array('Other', $selectedAreaofworks) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_area_of_work" class="form-control mb-2" id="other_area_of_work" value="{{ old('other_area_of_work', $user->other_area_of_work) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("area_of_work", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="how_did_you_hear_about" class="form-label text-muted mb-0 mt-2">HOW DID YOU HEAR ABOUT THE CUGH CONFERENCE <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                @php
                                                    $selectedHowdidyouhearabout = old('how_did_you_hear_about', $user->how_did_you_hear_about ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="My institution is a member" {{ in_array('My institution is a member', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">My institution is a member</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Promotional emails from CUGH" {{ in_array('Promotional emails from CUGH', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Promotional emails from CUGH</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="CUGH newsletters" {{ in_array('CUGH newsletters', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">CUGH newsletters</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Saw a flyer/information via non-CUGH site" {{ in_array('Saw a flyer/information via non-CUGH site', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Saw a flyer/information via non-CUGH site</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="From social media (Facebook, Twitter, etc.)" {{ in_array('From social media (Facebook, Twitter, etc.)', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">From social media (Facebook, Twitter, etc.)</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Through a colleague/friend" {{ in_array('Through a colleague/friend', $selectedHowdidyouhearabout) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Through a colleague/friend</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_did_you_hear_about[]" value="Other" {{ in_array('Other', $selectedHowdidyouhearabout) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" class="form-control mb-2" id="other_how_did_you_hear_about" name="other_how_did_you_hear_about" value="{{ old('other_how_did_you_hear_about', $user->other_how_did_you_hear_about) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("how_did_you_hear_about", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="why_attending" class="form-label text-muted mb-0 mt-2">WHY ARE YOU ATTENDING THE CONFERENCE? <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                @php
                                                    $selectedWhyattending = old('why_attending', $user->why_attending ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To learn about the latest in global health issues" {{ in_array('To learn about the latest in global health issues', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To learn about the latest in global health issues</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To network" {{ in_array('To network', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To network</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To find funding" {{ in_array('To find funding', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To find funding</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To identify a new educational/research opportunity" {{ in_array('To identify a new educational/research opportunity', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To identify a new educational/research opportunity</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To find a job" {{ in_array('To find a job', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To find a job</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To interact with speakers/presenters/moderators" {{ in_array('To interact with speakers/presenters/moderators', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To interact with speakers/presenters/moderators</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="To present my work to peers, colleagues, topic experts" {{ in_array('To present my work to peers, colleagues, topic experts', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">To present my work to peers, colleagues, topic experts</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="why_attending[]" value="Other" {{ in_array('Other', $selectedWhyattending) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_why_attending" class="form-control mb-2" id="other_why_attending" value="{{ old('other_why_attending', $user->other_why_attending) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("why_attending", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="ability_to_present_work" class="form-label text-muted mb-0 mt-2">HOW MUCH DOES THE ABILITY TO PRESENT YOUR WORK AFFECT YOUR ABILITY TO ATTEND THE CONFERENCE? <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-check-label d-block">
                                                            <input class="form-check-input" type="radio" name="ability_to_present_work" value="Essential" {{ old('ability_to_present_work', $user->ability_to_present_work) == 'Essential' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Essential</span>
                                                        </label>
                                                        <label class="form-check-label d-block">
                                                            <input class="form-check-input" type="radio" name="ability_to_present_work" value="Desirable but not essential" {{ old('ability_to_present_work', $user->ability_to_present_work) == 'Desirable but not essential' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Desirable but not essential</span>
                                                        </label>
                                                        <label class="form-check-label d-block">
                                                            <input class="form-check-input" type="radio" name="ability_to_present_work" value="No Effect" {{ old('ability_to_present_work', $user->ability_to_present_work) == 'No Effect' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">No Effect</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("ability_to_present_work", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="how_is_your_attendance_funded" class="form-label text-muted mb-0 mt-2">HOW IS YOUR ATTENDANCE FUNDED? <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                @php
                                                    $selectedHowisyourattendancefunded = old('how_is_your_attendance_funded', $user->how_is_your_attendance_funded ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="By myself" {{ in_array('By myself', $selectedHowisyourattendancefunded) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">By myself</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="By my program/school university" {{ in_array('By my program/school university', $selectedHowisyourattendancefunded) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">By my program/school university</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="By my place of employment" {{ in_array('By my place of employment', $selectedHowisyourattendancefunded) ? 'checked' : '' }} disabled>                                                            <span class="form-check-label">By my place of employment</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="how_is_your_attendance_funded[]" value="Other" {{ in_array('Other', $selectedHowisyourattendancefunded) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" class="form-control mb-2" name="other_how_is_your_attendance_funded" id="other_how_is_your_attendance_funded" value="{{ old('other_how_is_your_attendance_funded', $user->other_how_is_your_attendance_funded) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("how_is_your_attendance_funded", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-1">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="your_areas_of_focus_in_global_health" class="form-label text-muted mb-0 mt-2">YOUR AREAS OF FOCUS IN GLOBAL HEALTH <span class="text-danger">*</span> <small>(Check all that apply)</small></label>
                                                @php
                                                    $selectedYourareasglobal = old('your_areas_of_focus_in_global_health', $user->your_areas_of_focus_in_global_health ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Administration" {{ in_array('Administration', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Administration</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Advocacy/Communication" {{ in_array('Advocacy/Communication', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Advocacy/Communication</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Capacity Building" {{ in_array('Capacity Building', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Capacity Building</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Disaster Management" {{ in_array('Disaster Management', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Disaster Management</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Education" {{ in_array('Education', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Education</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Emergency Medicine" {{ in_array('Emergency Medicine', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Emergency Medicine</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Environment/One Health/Planetary Health" {{ in_array('Environment/One Health/Planetary Health', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Environment/One Health/Planetary Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Governance" {{ in_array('Governance', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Governance</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Economics" {{ in_array('Economics', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Economics</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Policy" {{ in_array('Policy', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Policy</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Implementation" {{ in_array('Implementation', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Implementation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Infectious Diseases/Pandemic prevention and response" {{ in_array('Infectious Diseases/Pandemic prevention and response', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Infectious Diseases/Pandemic prevention and response</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Mental Health" {{ in_array('Mental Health', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Mental Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="NCDS" {{ in_array('NCDS', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">NCDS</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Nutrition/Food Security" {{ in_array('Nutrition/Food Security', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Nutrition/Food Security</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Oral Health" {{ in_array('Oral Health', $selectedYourareasglobal) ? 'checked' : '' }} disabled>                                                            <span class="form-check-label">Oral Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Pediatrics" {{ in_array('Pediatrics', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Pediatrics</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Politics/Political Science" {{ in_array('Politics/Political Science', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Politics/Political Science</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Public Health" {{ in_array('Public Health', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Public Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Research" {{ in_array('Research', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Research</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Social Sciences" {{ in_array('Social Sciences', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Social Sciences</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Student Services/International Education" {{ in_array('Student Services/International Education', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Student Services/International Education</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Surgery/Trauma" {{ in_array('Surgery/Trauma', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Surgery/Trauma</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Veterinary Sciences" {{ in_array('Veterinary Sciences', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Veterinary Sciences</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Water/Sanitation" {{ in_array('Water/Sanitation', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Water/Sanitation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Women's Health" {{ in_array("Women's Health", $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Women's Health</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="your_areas_of_focus_in_global_health[]" value="Other" {{ in_array('Other', $selectedYourareasglobal) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_your_areas_of_focus_in_global_health" id="other_your_areas_of_focus_in_global_health" class="form-control mb-2" value="{{ old('other_your_areas_of_focus_in_global_health', $user->other_your_areas_of_focus_in_global_health) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("your_areas_of_focus_in_global_health", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="obstacles_to_attending_cughs_conferences" class="form-label text-muted mb-0 mt-2">OBSTACLES TO ATTENDING CUGH'S CONFERENCES <span class="text-danger">*</span></label>
                                                @php
                                                    $selectedObstaclestoattendingcughsconferences = old('obstacles_to_attending_cughs_conferences', $user->obstacles_to_attending_cughs_conferences ?? []);
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="obstacles_to_attending_cughs_conferences[]" value="Financial" {{ in_array('Financial', $selectedObstaclestoattendingcughsconferences) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Financial</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="obstacles_to_attending_cughs_conferences[]" value="Visas & other immigration factors" {{ in_array('Visas & other immigration factors', $selectedObstaclestoattendingcughsconferences) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Visas & other immigration factors</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" name="obstacles_to_attending_cughs_conferences[]" value="Other" {{ in_array('Other', $selectedObstaclestoattendingcughsconferences) ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">Other</span>
                                                        </label>
                                                        <input type="text" name="other_obstacles_to_attending_cughs_conferences" id="other_obstacles_to_attending_cughs_conferences" class="form-control mb-2" value="{{ old('other_obstacles_to_attending_cughs_conferences', $user->other_obstacles_to_attending_cughs_conferences) }}" placeholder="Please specify" disabled>
                                                    </div>
                                                </div>
                                                {!!$errors->first("obstacles_to_attending_cughs_conferences", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="receive_news_and_updates" class="form-label text-muted mb-0 mt-2">I WANT TO RECEIVE NEWS AND UPDATES ABOUT FUTURE CUGH ACTIVITIES AND EVENTS <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="receive_news_and_updates" value="Yes" {{ old('receive_news_and_updates', $user->receive_news_and_updates) == 'Yes' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">YES, I wish to receive CUGH news</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="receive_news_and_updates" value="No" {{ old('receive_news_and_updates', $user->receive_news_and_updates) == 'No' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">NO, I do not wish to receive CUGH news</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("receive_news_and_updates", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                {{-- CONTACT INFO --}}
                                                <label for="contact_info" class="form-label text-muted mb-0 mt-2">CONTACT INFORMATION <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="contact_info" value="I agree that my contact information can be shared with other attendees (Conference App)" {{ old('contact_info', $user->contact_info) == 'I agree that my contact information can be shared with other attendees (Conference App)' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">I agree that my contact information can be shared with other attendees (Conference App).</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="contact_info" value="I do not wish my contact information to be shared with other attendees" {{ old('contact_info', $user->contact_info) == 'I do not wish my contact information to be shared with other attendees' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">I do not wish my contact information to be shared with other attendees</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("contact_info", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                {{-- ORAL/POSTER ABSTRACT PRESENTER? (NOT APPLICABLE FOR PANEL SPEAKERS) --}}
                                                <label for="oral_poster_abstract_presenter" class="form-label text-muted mb-0 mt-2">ORAL/POSTER ABSTRACT PRESENTER? <span class="text-danger">*</span> <small>(Not Applicable for Panel Speakers)</small></label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="oral_poster_abstract_presenter" value="Yes" {{ old('oral_poster_abstract_presenter', $user->oral_poster_abstract_presenter) == 'Yes' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">YES, I will present a poster or oral abstract presentation</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="oral_poster_abstract_presenter" value="No" {{ old('oral_poster_abstract_presenter', $user->oral_poster_abstract_presenter) == 'No' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">NO, I am not presenting a poster or oral abstract presentation</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("oral_poster_abstract_presenter", "<span class='text-danger'>:message</span>")!!}
                                                <hr class="my-2">
                                            </div>
                                            <div class="col-md-12">
                                                {{-- PANEL PRESENTER/MODERATOR? (NOT APPLICABLE FOR SCIENTIFIC ABSTRACT SUBMITTERS) --}}
                                                <label for="panel_presenter_moderator" class="form-label text-muted mb-0 mt-2">PANEL PRESENTER/MODERATOR? <span class="text-danger">*</span> <small>(Not Applicable for Scientific Abstract Submitters)</small></label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="panel_presenter_moderator" value="Yes" {{ old('panel_presenter_moderator', $user->panel_presenter_moderator) == 'Yes' ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">YES, I will be a panel speaker/moderator</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="radio" name="panel_presenter_moderator" value="No" {{ old('panel_presenter_moderator', $user->panel_presenter_moderator) == 'No' ? 'checked' : '' }} disabled >
                                                            <span class="form-check-label">NO, I am not a panel speaker/moderator</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                {!!$errors->first("panel_presenter_moderator", "<span class='text-danger'>:message</span>")!!}
                                                
                                            </div>
                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@if(\Auth::user()->hasRole('Administrador'))
<div class="modal fade" id="privateNoteModal" tabindex="-1" aria-labelledby="privateNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('inscriptions.private-note.update', $inscription->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="privateNoteModalLabel">Private note · Registration #{{ $inscription->id }}</h5>
                    <button type="button" class="registration-modal-dismiss" data-bs-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 5l14 14M19 5L5 19"/></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="private_note" class="form-label">Note for administrators</label>
                    <textarea id="private_note" name="private_note" rows="7" maxlength="5000" class="form-control @error('private_note') is-invalid @enderror" placeholder="Write a private note about this registration...">{{ old('private_note', $inscription->private_note) }}</textarea>
                    @error('private_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Only administrators can view or update this note. Clear the text and save to remove it.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn registration-note-action registration-note-close" data-bs-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 5l14 14M19 5L5 19"/></svg>
                        Close
                    </button>
                    <button type="submit" class="btn registration-note-action registration-note-save">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12l5 5L20 6"/></svg>
                        Update Note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {

    @if(\Auth::user()->hasRole('Administrador') && $errors->has('private_note'))
        const privateNoteModal = document.getElementById('privateNoteModal');
        if (privateNoteModal && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(privateNoteModal).show();
        }
    @endif

    document.querySelectorAll('.confirm-form').forEach(function(form) {

        form.addEventListener('submit', function(e) {

            // Mostrar alerta de confirmación
            const confirmAction = confirm('Are you sure you want to confirm this inscription?');

            // Si cancela
            if (!confirmAction) {
                e.preventDefault();
                return;
            }

            // Bloquear botón para evitar doble click
            const button = form.querySelector('.submit-btn');

            button.disabled = true;
            button.innerHTML = 'Processing...';

        });

    });

});
</script>

@endsection
