<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\AidDistributionsImport;
use App\Models\AidDistribution;
use App\Models\AidDistributionImportBatch;
use App\Models\Project;
use App\Services\AidDistributionImportService;
use App\Services\DashboardService;
use App\Services\ProjectConsumptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class HomeController extends Controller
{
    public function index(DashboardService $dashboardService)
    {
        $year = Carbon::now()->year;
        $globalStats = $dashboardService->getGlobalStats();
        $monthlyStats = $dashboardService->getMonthlyStats();
        $officeStats = $dashboardService->getOfficeStats();
        $institutionStats = $dashboardService->getInstitutionStats();
        $topAidItems = $dashboardService->getTopAidItems();
        $recentDistributions = $dashboardService->getRecentDistributions();
        $projectStats = $dashboardService->getProjectStats();
        $showStorageOfficesBalance = Auth::user()?->user_type !== 'employee';

        $projectIds = $projectStats->pluck('id')->filter()->unique()->values()->all();
        $projects = Project::findMany($projectIds);
        $canEditMap = $projects->mapWithKeys(fn ($p) => [$p->id => Auth::user()?->can('update', $p) ?? false])->all();
        $projectStats->getCollection()->transform(function ($item) use ($canEditMap) {
            $item['can_edit'] = $canEditMap[$item['id']] ?? false;
            return $item;
        });

        return view('dashboard.index', compact(
            'year',
            'globalStats',
            'monthlyStats',
            'officeStats',
            'institutionStats',
            'topAidItems',
            'recentDistributions',
            'projectStats',
            'showStorageOfficesBalance'
        ));
    }

    public function refreshDashboardCache(DashboardService $dashboardService)
    {
        $dashboardService->clearDashboardCache();

        return redirect()
            ->route('dashboard.home')
            ->with('success', 'تم تحديث كاش الإحصائيات بنجاح');
    }

    public function import(Request $request)
    {
        $batchUuid = $request->query('batch');
        $batch = null;

        $allDecisionsMade = true;
        if ($batchUuid) {
            $batch = AidDistributionImportBatch::query()
                ->where('uuid', $batchUuid)
                ->with(['rows' => function ($q) {
                    $q->where(function ($query) {
                        $query->where('duplicate_in_file', true)
                            ->orWhere('duplicate_in_db', true);
                    })->orderBy('row_number');
                }])
                ->first();
            if ($batch) {
                $allDecisionsMade = $batch->rows()->where('decision', 'pending')->count() === 0;
            }
        }

        return view('dashboard.import', compact('batch', 'allDecisionsMade'));
    }

    public function import_excel(Request $request, AidDistributionImportService $importService)
    {
        $this->authorize('import', AidDistribution::class);
        
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'file.required' => 'الرجاء اختيار ملف للاستيراد',
            'file.mimes' => 'يجب أن يكون الملف بصيغة Excel (xlsx أو xls)',
            'file.max' => 'حجم الملف يجب أن لا يتجاوز 10 ميجابايت',
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        try {
            $batch = $importService->parseAndValidateFile($file, $filename);

            if ($batch->status === 'failed') {
                return redirect()
                    ->route('dashboard.import')
                    ->with('danger', 'فشل استيراد الملف - يرجى مراجعة الأخطاء أدناه')
                    ->with('import_errors', $batch->errors);
            }

            if ($batch->duplicate_rows > 0) {
                return redirect()->route('dashboard.import', ['batch' => $batch->uuid])
                    ->with('info', 'تم العثور على سجلات مكررة تحتاج موافقتك');
            }

            $result = $importService->finalizeImport($batch, app(ProjectConsumptionService::class));

            if (!$result['success']) {
                return redirect()
                    ->route('dashboard.import')
                    ->with('danger', 'فشل الاستيراد: ' . ($result['error'] ?? 'خطأ غير معروف'))
                    ->with('constraint_errors', $result['details'] ?? null);
            }

            return redirect()->route('dashboard.import')->with('success', "تم استيراد {$result['imported']} سجل بنجاح");
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard.import')
                ->with('danger', 'حدث خطأ أثناء معالجة الملف: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function import_finalize(string $uuid, AidDistributionImportService $importService, ProjectConsumptionService $consumptionService)
    {
        $this->authorize('import', AidDistribution::class);

        $batch = AidDistributionImportBatch::query()->where('uuid', $uuid)->firstOrFail();

        if ($batch->status !== 'pending_review') {
            return redirect()->route('dashboard.import')
                ->with('warning', 'هذه الدفعة تم معالجتها مسبقاً أو تم إلغاؤها');
        }

        $pendingCount = $batch->rows()->where('decision', 'pending')->count();
        if ($pendingCount > 0) {
            return redirect()->route('dashboard.import', ['batch' => $uuid])
                ->with('danger', 'يجب الموافقة أو الرفض على جميع السجلات المكررة قبل تنفيذ الاستيراد');
        }

        try {
            $result = $importService->finalizeImport($batch, $consumptionService);

            if (!$result['success']) {
                return redirect()
                    ->route('dashboard.import', ['batch' => $uuid])
                    ->with('danger', 'فشل الاستيراد النهائي: ' . ($result['error'] ?? 'خطأ غير معروف'))
                    ->with('constraint_errors', $result['details'] ?? null);
            }

            return redirect()->route('dashboard.import')
                ->with('success', "تم استيراد {$result['imported']} سجل بنجاح ✓");
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard.import', ['batch' => $uuid])
                ->with('danger', 'حدث خطأ أثناء الاستيراد النهائي: ' . $e->getMessage());
        }
    }

    public function import_update_decision(Request $request, string $uuid)
    {
        $this->authorize('import', AidDistribution::class);

        $validated = $request->validate([
            'row_id' => 'required|exists:aid_distribution_import_rows,id',
            'decision' => 'required|in:approved,rejected',
        ]);

        $batch = AidDistributionImportBatch::query()->where('uuid', $uuid)->firstOrFail();
        $row = $batch->rows()->findOrFail($validated['row_id']);

        $row->update([
            'decision' => $validated['decision'],
        ]);

        $allDecisionsMade = $batch->rows()->where('decision', 'pending')->count() === 0;

        return response()->json(['success' => true, 'all_decisions_made' => $allDecisionsMade]);
    }

}

