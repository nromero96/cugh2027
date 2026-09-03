<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Exports\WorkshopExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;


class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'category_name' => 'workshops',
            'page_name' => 'workshops',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        //get invitations
        $workshops = Workshop::orderBy('id', 'desc')->get();

        return view('pages.workshops.index', $data, compact('workshops'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function exportExcel()
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('Administrador'), 403);

        return Excel::download(new WorkshopExport(), 'Workshops_'.now()->format('Ymd_His').'.xlsx');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Workshop  $workshop
     * @return \Illuminate\Http\Response
     */
    public function show(Workshop $workshop)
    {

        //solo el administrador puede ver el workshop
        if (!auth()->user()->hasRole('Administrador')) {
            return redirect()->route('workshops.index')
                ->with('error', 'You do not have permission to view this workshop.');
        }

        $data = [
            'category_name' => 'workshops',
            'page_name' => 'workshops_show',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $workshop = Workshop::find($workshop->id);

        return view('pages.workshops.show', $data, compact('workshop'));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Workshop  $workshop
     * @return \Illuminate\Http\Response
     */
    public function edit(Workshop $workshop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Workshop  $workshop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Workshop $workshop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Workshop  $workshop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Workshop $workshop)
    {
        //
    }


    public function registerWorkshop(Request $request)
    {
        $submissionToken = \Illuminate\Support\Facades\Crypt::encryptString((string) Str::uuid());

        return view('pages.workshops.form-online', compact('submissionToken'));
    }

    public function storeWorkshop(\App\Http\Requests\StoreWorkshopRequest $request)
    {
        $input = $request->validated();

        try {
            $submissionId = \Illuminate\Support\Facades\Crypt::decryptString($input['submission_token']);
            if (!Str::isUuid($submissionId)) {
                throw new \RuntimeException('Invalid workshop submission identifier.');
            }
        } catch (\Throwable $exception) {
            return back()->withErrors(['submission_token' => 'Your form has expired. Please reload the page and try again.'])
                ->withInput($request->except('signature', '_token', 'submission_token'));
        }

        $filename = $submissionId.'.png';
        $path = 'uploads/workshops/'.$filename;
        $lock = null;
        $acquired = false;
        $created = false;
        $fileWritten = false;

        try {
            $lock = \Illuminate\Support\Facades\Cache::lock('workshop-submission:'.$submissionId, 120);
            $acquired = $lock->get();

            if (!$acquired) {
                return back()->withErrors(['submission' => 'Your application is being processed. Please wait before trying again.'])
                    ->withInput($request->except('signature', '_token'));
            }

            // Repeated requests with the same form token must not create a second application.
            if (Workshop::where('signature_path', $filename)->exists()) {
                return redirect()->route('workshops.registerworkshop')
                    ->with('success', 'Workshop application already submitted successfully.');
            }

            $image = \App\Http\Requests\StoreWorkshopRequest::decodeSignature($input['signature']);
            if ($image === null) {
                throw new \RuntimeException('Invalid workshop signature.');
            }
            $fileWritten = true;
            if (!Storage::disk('public')->put($path, $image)) {
                throw new \RuntimeException('Unable to store the workshop signature.');
            }

            unset($input['submission_token'], $input['terms'], $input['signature']);
            $input['payment_lead_same'] = $input['payment_lead_same'] === 'Yes';
            $input['signature_path'] = $filename;

            \Illuminate\Support\Facades\DB::transaction(function () use ($input) {
                Workshop::create($input);
            });
            $created = true;

            return redirect()->route('workshops.registerworkshop')
                ->with('success', 'Workshop application submitted successfully.');
        } catch (\Throwable $exception) {
            if ($fileWritten && !$created) {
                try {
                    Storage::disk('public')->delete($path);
                } catch (\Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            report($exception);

            return back()->withErrors(['submission' => 'We could not save your application. Your information has been preserved. Please sign again and try submitting.'])
                ->withInput($request->except('signature', '_token'));
        } finally {
            if ($acquired && $lock) {
                try {
                    $lock->release();
                } catch (\Throwable $lockException) {
                    report($lockException);
                }
            }
        }
    }


    public function pdf(Workshop $workshop)
    {
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Workshop N° ' . $workshop->id);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        $signature = '';

        if (!empty($workshop->signature_path)) {
            $signaturePath = public_path('storage/uploads/workshops/' . $workshop->signature_path);

            if (file_exists($signaturePath)) {
                $signature = '<br><img src="' . $signaturePath . '" width="220"><br>';
            }
        }

        $html = '
        <style>
            h2 {
                font-size: 16px;
                
            }

            h4 {
                font-size: 12px;
            }

            .label {
                font-weight: bold;
                color: #000000;
            }

            .value {
                color: #333333;
                line-height: 1.5;
            }

            .item {
                margin-bottom: 8px;
            }

            hr {
                border: 0.5px solid #dddddd;
                margin-bottom: 0px;         }
        </style>

        <h2>Workshop N°: ' . e($workshop->id) . '</h2>
        <p><span class="label">Created:</span> ' . e($workshop->created_at) . '</p>
        <hr>
        <h4>Lead Contact Person</h4>

        <div class="item"><span class="label">Name:</span><br><span class="value">' . e($workshop->lead_name) . '</span></div>

        <div class="item"><span class="label">Institution:</span><br><span class="value">' . e($workshop->lead_institution) . '</span></div>

        <div class="item"><span class="label">Professional Title:</span><br><span class="value">' . e($workshop->lead_title) . '</span></div>

        <div class="item"><span class="label">E-mail:</span><br><span class="value">' . e($workshop->lead_email) . '</span></div>

        <div class="item"><span class="label">Phone number (with country & area code):</span><br><span class="value">' . e($workshop->lead_phone) . '</span></div>

        <div class="item"><span class="label">Cell Phone (with country code):</span><br><span class="value">' . e($workshop->lead_cell) . '</span></div>

        <h4>Workshop Program Description</h4>

        <div class="item"><span class="label">Workshop Title:</span><br><span class="value">' . e($workshop->workshop_title) . '</span></div>

        <div class="item"><span class="label">Workshop Description (max 200 words):</span><br><span class="value">' . nl2br(e($workshop->workshop_desc)) . '</span></div>

        <div class="item"><span class="label">Objectives / Skills:</span><br><span class="value">' . nl2br(e($workshop->workshop_objectives)) . '</span></div>

        <div class="item"><span class="label">Speakers and Facilitators:</span><br><span class="value">' . nl2br(e($workshop->workshop_speakers)) . '</span></div>

        <h4>Workshop Room Options</h4>

        <div class="item"><span class="label">Preferred Time Slot:</span><br><span class="value">' . e($workshop->time_slot) . '</span></div>

        <div class="item"><span class="label">Half or Full Day:</span><br><span class="value">' . e($workshop->day_length) . '</span></div>

        <div class="item"><span class="label">Preferred Room Set-up:</span><br><span class="value">' . e($workshop->room_setup) . '</span></div>

        <div class="item"><span class="label">Desired Number of Attendees:</span><br><span class="value">' . e($workshop->attendees) . '</span></div>

        <div class="item"><span class="label">Notes or Comments:</span><br><span class="value">' . nl2br(e($workshop->notes)) . '</span></div>

        <h4>Contact for Invoice</h4>

        <div class="item"><span class="label">Will the applying party be the lead contact person for payment?:</span><br><span class="value">' . ($workshop->payment_lead_same ? 'Yes' : 'No') . '</span></div>

        <br><br>

        <div class="item">
            ' . $signature . '
            <span class="value">' . e($workshop->place_date) . '</span>
        </div>
        ';

        $pdf->writeHTML($html, true, false, true, false, '');

        return response($pdf->Output('workshop-' . $workshop->id . '.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

}
