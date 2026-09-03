<?php

namespace Tests\Feature;

use App\Exports\WorkshopExport;
use App\Http\Controllers\WorkshopController;
use App\Models\User;
use App\Models\Workshop;
use Maatwebsite\Excel\Facades\Excel;
use Mockery;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class WorkshopExportTest extends TestCase
{
    public function test_non_administrators_cannot_export_workshops()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasRole')->with('Administrador')->andReturn(false);
        $this->actingAs($user);

        try {
            app(WorkshopController::class)->exportExcel();
            $this->fail('Export should be forbidden.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_administrators_can_download_workshop_export()
    {
        Excel::fake();
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('hasRole')->with('Administrador')->andReturn(true);
        $this->actingAs($user);
        $this->travelTo(now()->startOfDay());

        app(WorkshopController::class)->exportExcel();

        Excel::assertDownloaded('Workshops_'.now()->format('Ymd_His').'.xlsx', function ($export) {
            return $export instanceof WorkshopExport;
        });
    }

    public function test_export_maps_payment_contact_and_preserves_text_safely()
    {
        $export = new WorkshopExport();
        $workshop = new Workshop([
            'lead_name' => 'Workshop lead',
            'lead_phone' => '+0012345',
            'payment_lead_same' => 1,
            'payment_name' => 'Different contact',
        ]);
        $row = $export->map($workshop);
        $this->assertCount(count($export->headings()), $row);
        $this->assertSame('Workshop lead', $row[17]);
        $this->assertSame('+0012345', $row[21]);
        $workshop->payment_lead_same = 0;
        $this->assertSame('Different contact', $export->map($workshop)[17]);

        $sheet = (new Spreadsheet())->getActiveSheet();
        $export->bindValue($sheet->getCell('A1'), '=1+1');
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A1')->getDataType());
    }
}
