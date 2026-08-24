<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Models\Program;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller{
    public function index(){
        // $category_name = '';
        $data = [
            'category_name' => 'dashboard',
            'page_name' => 'dashboard',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];
        
        //revisar si tiene alguna inscripcion en estado Pagado
        $myinscription = Inscription::where('status', 'Pagado')
                                    ->where('user_id', auth()->user()->id)
                                    ->first();
        $assistance = Inscription::where('status', 'Pagado')
                                    ->where('assistance', '!=', null)
                                    ->where('user_id', auth()->user()->id)
                                    ->first();

        if($myinscription){
            //$myprograms = Program::where('insc_id', '503')->get();
            $myprograms = Program::where('insc_id', $myinscription->id)->get();
        } else {
            $myprograms = '[]';
        }

        $registrationReport = null;

        if (auth()->user()->hasRole('Administrador')) {
            $statusCounts = Inscription::select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            $categoryCounts = Inscription::leftJoin(
                    'category_inscriptions',
                    'inscriptions.category_inscription_id',
                    '=',
                    'category_inscriptions.id'
                )
                ->selectRaw("COALESCE(category_inscriptions.name, 'No category selected') as category_name, COUNT(inscriptions.id) as total")
                ->groupBy('category_inscriptions.id', 'category_inscriptions.name')
                ->orderByDesc('total')
                ->get();

            $paymentCounts = Inscription::selectRaw("COALESCE(payment_method, 'Not selected') as payment_method, COUNT(*) as total")
                ->groupBy('payment_method')
                ->orderByDesc('total')
                ->get();

            $completedStatuses = ['Paid', 'Confirmed', 'Pagado'];
            $completed = collect($completedStatuses)->sum(function ($status) use ($statusCounts) {
                return (int) ($statusCounts[$status] ?? 0);
            });

            $registrationReport = [
                'total' => (int) $statusCounts->sum(),
                'completed' => $completed,
                'in_progress' => (int) ($statusCounts['Processing'] ?? 0) + (int) ($statusCounts['Pending'] ?? 0),
                'drafts' => (int) ($statusCounts['Draft'] ?? 0),
                'status_counts' => $statusCounts,
                'category_counts' => $categoryCounts,
                'payment_counts' => $paymentCounts,
            ];
        }

        return view('dashboard.index')->with($data)->with('myinscription', $myinscription)->with('myprograms', $myprograms)->with('assistance', $assistance)->with('registrationReport', $registrationReport);
    }


}
