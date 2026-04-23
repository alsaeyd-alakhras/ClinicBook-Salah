<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AidDistribution;
use App\Models\AidItem;
use App\Models\Family;
use App\Models\Institution;
use App\Models\Office;
use App\Models\Project;
use App\Exports\AidDistributionsExport;
use App\Services\ProjectConsumptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class AidDistributionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeLookupForAidDistribution();
        $user = Auth::user();
        if($user && $user->user_type == 'employee') {
            $office_id = $user?->office_id;
        } else {
            $office_id = null;
        }

        if ($request->ajax()) {
            $year = $request->year ?? Carbon::now()->year;

            $distributions = AidDistribution::query()
                ->with(['family', 'office', 'institution', 'aidItem', 'creator', 'project'])
                ->whereYear('distributed_at', $year);

            if ($request->from_date) {
                $distributions->whereDate('distributed_at', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $distributions->whereDate('distributed_at', '<=', $request->to_date);
            }

            if ($request->column_filters) {
                $this->applyColumnFilters($distributions, $request->column_filters);
            }
            if ($request->project_id) {
                $distributions->where('project_id', $request->project_id);
            }
            if ($request->office_id && !$office_id) {
                $distributions->where('office_id', $request->office_id);
            }
            if ($office_id) {
                $distributions->where('office_id', $office_id);
            }
            if ($request->family_id) {
                $distributions->where('family_id', $request->family_id);
            }

            $this->applySort($distributions, $request->sort_column, $request->sort_direction);

            $rows = $distributions->get()->map(function (AidDistribution $distribution) {
                $family = $distribution->family;

                return [
                    'id' => $distribution->id,
                    'distributed_at' => optional($distribution->distributed_at)->format('Y-m-d'),
                    'primary_name' => $family?->full_name ?? '-',
                    'national_id' => $family?->national_id ?? '-',
                    'housing_location' => $family?->address ?? '-',
                    'family_members_count' => $family?->family_members_count ?? '-',
                    'marital_status' => $this->translateMaritalStatus($family?->marital_status),
                    'office_name' => $distribution->office?->name ?? '-',
                    'institution_name' => $distribution->institution?->name ?? '-',
                    'project_name' => $distribution->project ? ($distribution->project->project_number . ' - ' . $distribution->project->name) : '-',
                    'aid_mode' => $distribution->aid_mode,
                    'aid_value' => $distribution->aid_mode === 'cash'
                        ? ($distribution->cash_amount ?? 0)
                        : ($distribution->aidItem?->name ?? '-'),
                    'quantity' => $distribution->aid_mode === 'in_kind'
                        ? ($distribution->quantity !== null ? number_format((float) $distribution->quantity, 2) : '-')
                        : '-',
                    'mobile' => $family?->phone ?? '-',
                    'creator_name' => $distribution->creator?->name ?? '-',
                ];
            })->values();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('edit', function ($row) {
                    return $row['id'];
                })
                ->addColumn('delete', function ($row) {
                    return $row['id'];
                })
                ->make(true);
        }

        return view('dashboard.aid_distributions.index');
    }

    public function exportExcel(Request $request)
    {
        $this->authorizeLookupForAidDistribution();

        $user = Auth::user();
        $office_id = ($user && $user->user_type === 'employee') ? $user->office_id : null;

        $year = $request->year ?? Carbon::now()->year;
        $columnFilters = $request->column_filters;
        if (is_string($columnFilters)) {
            $columnFilters = json_decode($columnFilters, true) ?? [];
        }

        $distributions = AidDistribution::query()
            ->with(['family', 'office', 'institution', 'aidItem', 'creator', 'project'])
            ->whereYear('distributed_at', $year)
            ->orderBy('distributed_at', 'desc');

        if ($request->from_date) {
            $distributions->whereDate('distributed_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $distributions->whereDate('distributed_at', '<=', $request->to_date);
        }
        if (!empty($columnFilters)) {
            $this->applyColumnFilters($distributions, $columnFilters);
        }
        if ($request->project_id) {
            $distributions->where('project_id', $request->project_id);
        }
        if ($request->office_id && !$office_id) {
            $distributions->where('office_id', $request->office_id);
        }
        if ($office_id) {
            $distributions->where('office_id', $office_id);
        }
        if ($request->family_id) {
            $distributions->where('family_id', $request->family_id);
        }

        $rows = $distributions->get()->map(function (AidDistribution $distribution) {
            $family = $distribution->family;

            return [
                'id' => $distribution->id,
                'distributed_at' => optional($distribution->distributed_at)->format('Y-m-d'),
                'primary_name' => $family?->full_name ?? '-',
                'national_id' => $family?->national_id ?? '-',
                'housing_location' => $family?->address ?? '-',
                'family_members_count' => $family?->family_members_count ?? '-',
                'marital_status' => $this->translateMaritalStatus($family?->marital_status),
                'office_name' => $distribution->office?->name ?? '-',
                'institution_name' => $distribution->institution?->name ?? '-',
                'project_name' => $distribution->project ? ($distribution->project->project_number . ' - ' . $distribution->project->name) : '-',
                'aid_mode' => $distribution->aid_mode,
                'aid_value' => $distribution->aid_mode === 'cash'
                    ? ($distribution->cash_amount ?? 0)
                    : ($distribution->aidItem?->name ?? '-'),
                'quantity' => $distribution->aid_mode === 'in_kind'
                    ? ($distribution->quantity !== null ? number_format((float) $distribution->quantity, 2) : '-')
                    : '-',
                'mobile' => $family?->phone ?? '-',
                'creator_name' => $distribution->creator?->name ?? '-',
            ];
        })->values();

        $filename = 'مساعدات_' . $year . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new AidDistributionsExport($rows), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function create()
    {
        $this->authorize('create', AidDistribution::class);

        $offices = Office::query()->where('is_active', true)->orderBy('name')->get();
        $institutions = Institution::query()->where('is_active', true)->orderBy('name')->get();
        $aidItems = AidItem::query()->where('is_active', true)->orderBy('name')->get();
        $projects = Project::query()->orderBy('project_number')->get();

        $distribution = new AidDistribution([
            'aid_mode' => 'cash',
            'distributed_at' => now()->format('Y-m-d\TH:i'),
            'office_id' => Auth::user()?->office_id,
        ]);
        $familyForm = null;
        $isEdit = false;

        return view('dashboard.aid_distributions.create', compact('offices', 'institutions', 'aidItems', 'projects', 'distribution', 'familyForm', 'isEdit'));
    }

    public function store(Request $request, ProjectConsumptionService $consumptionService)
    {
        $this->authorize('create', AidDistribution::class);

        try {
            $validated = $this->validateForm($request);
            $validated['office_id'] = $this->resolveOfficeIdForStore($validated);

            $consumptionService->createDistribution($validated);

            return redirect()->route('dashboard.aid-distributions.index')
                ->with('success', 'تم حفظ عملية المساعدة بنجاح ✓');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('danger', 'حدث خطأ أثناء حفظ المساعدة: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function edit(AidDistribution $aidDistribution)
    {
        $this->authorize('update', AidDistribution::class);

        $offices = Office::query()->where('is_active', true)->orderBy('name')->get();
        $institutions = Institution::query()->where('is_active', true)->orderBy('name')->get();
        $aidItems = AidItem::query()->where('is_active', true)->orderBy('name')->get();
        $projects = Project::query()->orderBy('project_number')->get();

        $family = $aidDistribution->family;
        $familyForm = $this->mapFamilyToForm($family);
        $distribution = $aidDistribution;
        $isEdit = true;

        return view('dashboard.aid_distributions.edit', compact('offices', 'institutions', 'aidItems', 'projects', 'distribution', 'familyForm', 'isEdit'));
    }

    public function show(AidDistribution $aidDistribution)
    {
        $this->authorize('update', AidDistribution::class);

        return redirect()->route('dashboard.aid-distributions.edit', $aidDistribution->id);
    }

    public function update(Request $request, AidDistribution $aidDistribution, ProjectConsumptionService $consumptionService)
    {
        $this->authorize('update', AidDistribution::class);

        try {
            $validated = $this->validateForm($request, $aidDistribution);
            $validated['office_id'] = $this->resolveOfficeIdForUpdate($validated, $aidDistribution);

            $consumptionService->updateDistribution($aidDistribution, $validated);

            return redirect()->route('dashboard.aid-distributions.index')
                ->with('success', 'تم تحديث العملية بنجاح ✓');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('danger', 'حدث خطأ أثناء تحديث المساعدة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(AidDistribution $aidDistribution, ProjectConsumptionService $consumptionService)
    {
        $this->authorize('delete', AidDistribution::class);

        $consumptionService->deleteDistribution($aidDistribution);

        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('dashboard.aid-distributions.index')->with('success', 'تم حذف العملية بنجاح');
    }

    public function getFilterOptions(Request $request, $column)
    {
        $this->authorizeLookupForAidDistribution();

        $year = $request->year ?? Carbon::now()->year;
        $user = Auth::user();
        $officeId = ($user && $user->user_type === 'employee') ? (int) $user->office_id : null;

        $query = AidDistribution::query()
            ->with(['family', 'office', 'institution', 'aidItem', 'creator', 'project'])
            ->whereYear('distributed_at', $year);

        if ($request->from_date) {
            $query->whereDate('distributed_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('distributed_at', '<=', $request->to_date);
        }

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->office_id && !$officeId) {
            $query->where('office_id', $request->office_id);
        }
        if ($officeId) {
            $query->where('office_id', $officeId);
        }
        if ($request->family_id) {
            $query->where('family_id', $request->family_id);
        }

        if ($request->active_filters) {
            $this->applyColumnFilters($query, $request->active_filters);
        }

        $rows = $query->get();
        $options = match ($column) {
            'distributed_at' => $rows->pluck('distributed_at')->filter()->map(fn ($d) => $d->format('Y-m-d'))->unique()->values()->toArray(),
            'office_name' => $rows->pluck('office.name')->filter()->unique()->values()->toArray(),
            'institution_name' => $rows->pluck('institution.name')->filter()->unique()->values()->toArray(),
            'project_name' => $rows->map(fn (AidDistribution $d) => $d->project ? $d->project->project_number . ' - ' . $d->project->name : null)->filter()->unique()->values()->toArray(),
            'aid_mode' => $rows->pluck('aid_mode')->filter()->unique()->values()->toArray(),
            'aid_value' => $rows->map(function (AidDistribution $d) {
                return $d->aid_mode === 'cash' ? (string) ($d->cash_amount ?? 0) : ($d->aidItem?->name ?? null);
            })->filter()->unique()->values()->toArray(),
            'quantity' => $rows->map(function (AidDistribution $d) {
                return $d->aid_mode === 'in_kind' && $d->quantity !== null
                    ? number_format((float) $d->quantity, 2)
                    : null;
            })->filter()->unique()->values()->toArray(),
            'primary_name' => $rows->pluck('family.full_name')->filter()->unique()->values()->toArray(),
            'national_id' => $rows->pluck('family.national_id')->filter()->unique()->values()->toArray(),
            'housing_location' => $rows->pluck('family.address')->filter()->unique()->values()->toArray(),
            'family_members_count' => $rows->pluck('family.family_members_count')->filter()->unique()->values()->toArray(),
            'marital_status' => $rows->pluck('family.marital_status')->filter()->map(fn ($value) => $this->translateMaritalStatus($value))->unique()->values()->toArray(),
            'mobile' => $rows->pluck('family.phone')->filter()->unique()->values()->toArray(),
            'creator_name' => $rows->pluck('creator.name')->filter()->unique()->values()->toArray(),
            default => [],
        };

        return response()->json($options);
    }

    private function validateForm(Request $request, ?AidDistribution $aidDistribution = null): array
    {
        $projectIdRule = 'required|exists:projects,id';
        
        if ($aidDistribution && $aidDistribution->project_id === null) {
            $projectIdRule = 'nullable|exists:projects,id';
        }

        $validated = $request->validate([
            'family_id' => 'nullable|exists:families,id',
            'resolution_mode' => 'nullable|in:attach_to_existing,create_new_family',
            'primary_name' => 'required|string|max:255',
            'national_id' => 'required|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'family_members_count' => 'nullable|integer|min:1',
            'housing_location' => 'nullable|string|max:255',
            'marital_status' => 'required|in:single,married,polygamous,widowed,divorced',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_national_id' => 'nullable|string|max:20',
            'spouses' => 'nullable|array|max:4',
            'spouses.*.full_name' => 'nullable|string|max:255',
            'spouses.*.national_id' => 'nullable|string|max:20',

            'office_id' => 'required|exists:offices,id',
            'institution_id' => 'required|exists:institutions,id',
            'project_id' => $projectIdRule,
            'aid_mode' => 'required|in:cash,in_kind',
            'cash_amount' => 'nullable|numeric|min:0|required_if:aid_mode,cash',
            'aid_item_id' => 'nullable|exists:aid_items,id|required_if:aid_mode,in_kind',
            'quantity' => 'nullable|numeric|min:0.01|required_if:aid_mode,in_kind',
            'distributed_date' => 'nullable|date',
            'distribution_notes' => 'nullable|string',
        ]);

        $spouses = $this->normalizedSpousesFromInput($validated);
        $status = $validated['marital_status'];

        if (in_array($status, ['single', 'widowed', 'divorced'], true) && !empty($spouses)) {
            throw ValidationException::withMessages([
                'spouses' => 'لا يمكن إدخال بيانات الزوجات عند اختيار حالة اجتماعية غير متزوج.',
            ]);
        }

        if ($status === 'married' && empty($spouses[0]['national_id'])) {
            throw ValidationException::withMessages([
                'spouses.0.national_id' => 'رقم هوية الزوجة الأولى مطلوب عند اختيار متزوج/ة.',
            ]);
        }

        if ($status === 'polygamous') {
            if (count($spouses) < 2) {
                throw ValidationException::withMessages([
                    'spouses' => 'في حالة متعدد الزوجات يجب إدخال زوجتين على الأقل.',
                ]);
            }

            if (empty($spouses[0]['national_id']) || empty($spouses[1]['national_id'])) {
                throw ValidationException::withMessages([
                    'spouses.0.national_id' => 'رقم هوية الزوجة الأولى مطلوب.',
                    'spouses.1.national_id' => 'رقم هوية الزوجة الثانية مطلوب.',
                ]);
            }
        }

        $wifeNationalIds = collect($spouses)->pluck('national_id')->filter()->values();
        if ($wifeNationalIds->count() !== $wifeNationalIds->unique()->count()) {
            throw ValidationException::withMessages([
                'spouses' => 'لا يمكن تكرار رقم هوية الزوجة أكثر من مرة.',
            ]);
        }

        if ($wifeNationalIds->contains($validated['national_id'])) {
            throw ValidationException::withMessages([
                'spouses' => 'لا يمكن أن يكون رقم هوية المستفيد الأساسي هو نفسه رقم هوية الزوجة.',
            ]);
        }

        $validated['spouses'] = $spouses;

        return $validated;
    }

    private function extractFamilyData(array $validated): array
    {
        $status = $validated['marital_status'];
        $spouses = in_array($status, ['married', 'polygamous'], true)
            ? $this->normalizedSpousesFromInput($validated)
            : [];
        $firstSpouse = $spouses[0] ?? null;

        return [
            'full_name' => $validated['primary_name'],
            'national_id' => $validated['national_id'],
            'phone' => $validated['mobile'] ?? null,
            'family_members_count' => $validated['family_members_count'] ?? null,
            'address' => $validated['housing_location'] ?? null,
            'marital_status' => $status,
            'spouses' => !empty($spouses) ? $spouses : null,
            // توافق مؤقت مع الحقول القديمة
            'spouse_full_name' => $firstSpouse['full_name'] ?? null,
            'spouse_national_id' => $firstSpouse['national_id'] ?? null,
        ];
    }

    private function mapFamilyToForm(Family $family): array
    {
        $spouses = $this->getFamilySpouses($family);
        $firstSpouse = $spouses[0] ?? null;

        return [
            'primary_name' => $family->full_name,
            'national_id' => $family->national_id,
            'mobile' => $family->phone,
            'family_members_count' => $family->family_members_count,
            'housing_location' => $family->address,
            'marital_status' => $family->marital_status ?? 'single',
            'spouses' => $spouses,
            // توافق مؤقت مع الحقول القديمة في الواجهة
            'spouse_name' => $firstSpouse['full_name'] ?? null,
            'spouse_national_id' => $firstSpouse['national_id'] ?? null,
        ];
    }

    private function resolveOfficeIdForStore(array $validated): int
    {
        if (!$this->isEmployeeUser()) {
            return (int) $validated['office_id'];
        }

        $employeeOfficeId = Auth::user()?->office_id;
        if (!$employeeOfficeId) {
            throw ValidationException::withMessages([
                'office_id' => 'لا يمكن إتمام الحفظ لأن مكتب المستخدم غير محدد.',
            ]);
        }

        return (int) $employeeOfficeId;
    }

    private function resolveOfficeIdForUpdate(array $validated, AidDistribution $aidDistribution): int
    {
        if ($this->isEmployeeUser()) {
            return (int) $aidDistribution->office_id;
        }

        return (int) $validated['office_id'];
    }

    private function isEmployeeUser(): bool
    {
        return Auth::user()?->user_type === 'employee';
    }

    private function applyColumnFilters($query, array $columnFilters): void
    {
        foreach ($columnFilters as $fieldName => $values) {
            if (empty($values)) {
                continue;
            }

            if ($fieldName === 'distributed_at' && is_array($values)) {
                if (isset($values['from'])) {
                    $query->whereDate('distributed_at', '>=', $values['from']);
                }
                if (isset($values['to'])) {
                    $query->whereDate('distributed_at', '<=', $values['to']);
                }
                continue;
            }

            $filteredValues = array_values(array_filter((array) $values, function ($value) {
                return !in_array($value, ['الكل', 'all', 'All'], true);
            }));

            if (empty($filteredValues)) {
                continue;
            }

            switch ($fieldName) {
                case 'office_name':
                    $query->whereHas('office', fn ($q) => $q->whereIn('name', $filteredValues));
                    break;
                case 'institution_name':
                    $query->whereHas('institution', fn ($q) => $q->whereIn('name', $filteredValues));
                    break;
                case 'project_name':
                    $projectNumbers = array_map(function ($val) {
                        $parts = explode(' - ', (string) $val, 2);
                        return trim($parts[0] ?? '');
                    }, $filteredValues);
                    $projectNumbers = array_filter($projectNumbers);
                    if (!empty($projectNumbers)) {
                        $query->whereHas('project', fn ($q) => $q->whereIn('project_number', $projectNumbers));
                    }
                    break;
                case 'creator_name':
                    $query->whereHas('creator', fn ($q) => $q->whereIn('name', $filteredValues));
                    break;
                case 'primary_name':
                    $query->whereHas('family', fn ($q) => $q->whereIn('full_name', $filteredValues));
                    break;
                case 'national_id':
                    $query->whereHas('family', fn ($q) => $q->whereIn('national_id', $filteredValues));
                    break;
                case 'mobile':
                    $query->whereHas('family', fn ($q) => $q->whereIn('phone', $filteredValues));
                    break;
                case 'family_members_count':
                    $query->whereHas('family', fn ($q) => $q->whereIn('family_members_count', $filteredValues));
                    break;
                case 'marital_status':
                    $maritalValues = array_map(function ($value) {
                        return match ($value) {
                            'متزوج/ة' => 'married',
                            'ارمل/ة' => 'widowed',
                            default => $value,
                        };
                    }, $filteredValues);
                    $query->whereHas('family', fn ($q) => $q->whereIn('marital_status', $maritalValues));
                    break;
                case 'housing_location':
                    $query->whereHas('family', fn ($q) => $q->whereIn('address', $filteredValues));
                    break;
                case 'aid_value':
                    $query->where(function ($subQ) use ($filteredValues) {
                        foreach ($filteredValues as $value) {
                            $subQ->orWhere('cash_amount', 'like', '%' . $value . '%')
                                ->orWhereHas('aidItem', fn ($q) => $q->where('name', 'like', '%' . $value . '%'));
                        }
                    });
                    break;
                case 'quantity':
                    $query->where(function ($subQ) use ($filteredValues) {
                        foreach ($filteredValues as $value) {
                            $subQ->orWhere('quantity', 'like', '%' . $value . '%');
                        }
                    });
                    break;
                default:
                    $query->whereIn($fieldName, $filteredValues);
                    break;
            }
        }
    }

    private function applySort($query, ?string $sortColumn, ?string $sortDirection): void
    {
        $dir = in_array(strtolower((string) $sortDirection), ['asc', 'desc'], true)
            ? strtolower($sortDirection)
            : null;

        if (empty($sortColumn) || $dir === null) {
            $query->orderBy('aid_distributions.distributed_at', 'desc');
            return;
        }

        $baseTable = 'aid_distributions';

        switch ($sortColumn) {
            case 'distributed_at':
                $query->orderBy("{$baseTable}.distributed_at", $dir);
                break;
            case 'primary_name':
                $query->leftJoin('families', "{$baseTable}.family_id", '=', 'families.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('families.full_name', $dir);
                break;
            case 'national_id':
                $query->leftJoin('families', "{$baseTable}.family_id", '=', 'families.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('families.national_id', $dir);
                break;
            case 'housing_location':
                $query->leftJoin('families', "{$baseTable}.family_id", '=', 'families.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('families.address', $dir);
                break;
            case 'family_members_count':
                $query->leftJoin('families', "{$baseTable}.family_id", '=', 'families.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('families.family_members_count', $dir);
                break;
            case 'marital_status':
                $query->leftJoin('families', "{$baseTable}.family_id", '=', 'families.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('families.marital_status', $dir);
                break;
            case 'office_name':
                $query->leftJoin('offices', "{$baseTable}.office_id", '=', 'offices.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('offices.name', $dir);
                break;
            case 'institution_name':
                $query->leftJoin('institutions', "{$baseTable}.institution_id", '=', 'institutions.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('institutions.name', $dir);
                break;
            case 'project_name':
                $query->leftJoin('projects', "{$baseTable}.project_id", '=', 'projects.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('projects.project_number', $dir);
                break;
            case 'aid_mode':
                $query->orderBy("{$baseTable}.aid_mode", $dir);
                break;
            case 'aid_value':
                $query->leftJoin('aid_items', "{$baseTable}.aid_item_id", '=', 'aid_items.id')
                    ->select("{$baseTable}.*")
                    ->orderByRaw("COALESCE(CAST({$baseTable}.cash_amount AS CHAR), aid_items.name) {$dir}");
                break;
            case 'quantity':
                $query->orderBy("{$baseTable}.quantity", $dir);
                break;
            case 'mobile':
                $query->leftJoin('families', "{$baseTable}.family_id", '=', 'families.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('families.phone', $dir);
                break;
            case 'creator_name':
                $query->leftJoin('users', "{$baseTable}.created_by", '=', 'users.id')
                    ->select("{$baseTable}.*")
                    ->orderBy('users.name', $dir);
                break;
            default:
                $query->orderBy("{$baseTable}.distributed_at", 'desc');
                break;
        }
    }

    private function translateMaritalStatus(?string $status): string
    {
        return match ($status) {
            'single' => 'أعزب/عزباء',
            'married' => 'متزوج/ة',
            'widowed' => 'ارمل/ة',
            'divorced' => 'مطلق/ة',
            'polygamous' => 'متعدد الزوجات',
            default => '-',
        };
    }

    /**
     * API: Search for family by national ID (primary or spouse)
     */
    public function searchByNationalId(string $id)
    {
        $this->authorizeLookupForAidDistribution();

        // البحث في العمودين: national_id أو spouse_national_id
        $primaryMatch = Family::query()->where('national_id', $id)->first();
        $spouseMatch = $this->findFamilyBySpouseNationalId($id);

        // تحديد نوع التطابق
        if ($primaryMatch) {
            $family = $primaryMatch;
            $matchType = 'primary_match';
        } elseif ($spouseMatch) {
            $family = $spouseMatch;
            $matchType = 'spouse_match';
        } else {
            return response()->json(['match_type' => 'no_match']);
        }

        // جلب آخر 10 مساعدات (status=active فقط)
        $aids = $family->distributions()
            ->with(['office', 'institution', 'aidItem'])
            ->where('status', 'active')
            ->orderBy('distributed_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function (AidDistribution $dist) {
                return [
                    'id' => $dist->id,
                    'office_name' => $dist->office?->name ?? '-',
                    'institution_name' => $dist->institution?->name ?? '-',
                    'distributed_at' => $dist->distributed_at?->format('Y-m-d') ?? '-',
                    'aid_mode' => $dist->aid_mode === 'cash' ? 'نقدية' : 'عينية',
                    'aid_value' => $dist->aid_mode === 'cash'
                        ? number_format($dist->cash_amount, 2) . ' ₪'
                        : ($dist->aidItem?->name ?? '-'),
                    'quantity' => $dist->aid_mode === 'in_kind' && $dist->quantity !== null
                        ? number_format((float) $dist->quantity, 2)
                        : '-',
                ];
            });

        // إجمالي عدد المساعدات
        $aidsTotal = $family->distributions()->where('status', 'active')->count();

        return response()->json([
            'match_type' => $matchType,
            'family' => [
                'id' => $family->id,
                'national_id' => $family->national_id,
                'full_name' => $family->full_name,
                'phone' => $family->phone,
                'family_members_count' => $family->family_members_count,
                'address' => $family->address,
                'marital_status' => $family->marital_status,
                'spouses' => $this->getFamilySpouses($family),
                'spouse_national_id' => $this->getFamilySpouses($family)[0]['national_id'] ?? null,
                'spouse_full_name' => $this->getFamilySpouses($family)[0]['full_name'] ?? null,
            ],
            'last_10_aids' => $aids,
            'total_aids' => $aidsTotal,
        ]);
    }

    /**
     * API: Get all aids for a family (for lazy load)
     */
    public function getAllAids(int $familyId)
    {
        $this->authorizeLookupForAidDistribution();

        $family = Family::findOrFail($familyId);

        $aids = $family->distributions()
            ->with(['office', 'institution', 'aidItem'])
            ->where('status', 'active')
            ->orderBy('distributed_at', 'desc')
            ->get()
            ->map(function (AidDistribution $dist) {
                return [
                    'id' => $dist->id,
                    'office_name' => $dist->office?->name ?? '-',
                    'institution_name' => $dist->institution?->name ?? '-',
                    'distributed_at' => $dist->distributed_at?->format('Y-m-d') ?? '-',
                    'aid_mode' => $dist->aid_mode === 'cash' ? 'نقدية' : 'عينية',
                    'aid_value' => $dist->aid_mode === 'cash'
                        ? number_format($dist->cash_amount, 2) . ' ₪'
                        : ($dist->aidItem?->name ?? '-'),
                    'quantity' => $dist->aid_mode === 'in_kind' && $dist->quantity !== null
                        ? number_format((float) $dist->quantity, 2)
                        : '-',
                ];
            });

        return response()->json([
            'aids' => $aids,
            'total' => $aids->count(),
        ]);
    }

    /**
     * API: Show single aid distribution details for modal
     */
    public function showAidDistribution(int $id)
    {
        $this->authorizeLookupForAidDistribution();

        $distribution = AidDistribution::with(['family', 'office', 'institution', 'aidItem', 'creator'])
            ->findOrFail($id);

        return response()->json([
            'distribution' => [
                'id' => $distribution->id,
                'office_name' => $distribution->office?->name ?? '-',
                'institution_name' => $distribution->institution?->name ?? '-',
                'aid_mode' => $distribution->aid_mode === 'cash' ? 'نقدية' : 'عينية',
                'cash_amount' => $distribution->cash_amount,
                'aid_item_name' => $distribution->aidItem?->name ?? '-',
                'quantity' => $distribution->quantity,
                'distributed_at' => $distribution->distributed_at?->format('Y-m-d') ?? '-',
                'notes' => $distribution->notes,
                'status' => $distribution->status,
                'creator_name' => $distribution->creator?->name ?? '-',
            ],
            'family' => [
                'full_name' => $distribution->family?->full_name ?? '-',
                'national_id' => $distribution->family?->national_id ?? '-',
                'phone' => $distribution->family?->phone ?? '-',
                'family_members_count' => $distribution->family?->family_members_count ?? '-',
                'address' => $distribution->family?->address ?? '-',
                'marital_status' => $this->translateMaritalStatus($distribution->family?->marital_status),
                'spouses' => $distribution->family ? $this->getFamilySpouses($distribution->family) : [],
                'spouse_full_name' => $distribution->family ? ($this->getFamilySpouses($distribution->family)[0]['full_name'] ?? '-') : '-',
                'spouse_national_id' => $distribution->family ? ($this->getFamilySpouses($distribution->family)[0]['national_id'] ?? '-') : '-',
            ],
        ]);
    }

    private function findFamilyBySpouseNationalId(string $nationalId): ?Family
    {
        return Family::query()
            ->where(function ($query) use ($nationalId) {
                $query->where('wife_1_national_id_gen', $nationalId)
                    ->orWhere('wife_2_national_id_gen', $nationalId)
                    ->orWhere('wife_3_national_id_gen', $nationalId)
                    ->orWhere('wife_4_national_id_gen', $nationalId)
                    // fallback مؤقت للسجلات القديمة قبل الترحيل الكامل
                    ->orWhere('spouse_national_id', $nationalId);
            })
            ->first();
    }

    private function normalizedSpousesFromInput(array $validated): array
    {
        $spouses = collect($validated['spouses'] ?? [])->map(function ($spouse) {
            $fullName = isset($spouse['full_name']) ? trim((string) $spouse['full_name']) : null;
            $nationalId = isset($spouse['national_id']) ? trim((string) $spouse['national_id']) : null;

            return [
                'full_name' => $fullName !== '' ? $fullName : null,
                'national_id' => $nationalId !== '' ? $nationalId : null,
            ];
        });

        // fallback للواجهة القديمة إن أرسلت الحقول المفردة
        $legacySpouseName = trim((string) ($validated['spouse_name'] ?? ''));
        $legacySpouseNationalId = trim((string) ($validated['spouse_national_id'] ?? ''));
        if ($spouses->isEmpty() && ($legacySpouseName !== '' || $legacySpouseNationalId !== '')) {
            $spouses = collect([[
                'full_name' => $legacySpouseName !== '' ? $legacySpouseName : null,
                'national_id' => $legacySpouseNationalId !== '' ? $legacySpouseNationalId : null,
            ]]);
        }

        return $spouses
            ->filter(fn ($spouse) => !empty($spouse['full_name']) || !empty($spouse['national_id']))
            ->take(4)
            ->values()
            ->toArray();
    }

    private function getFamilySpouses(Family $family): array
    {
        return $family->wives;
    }

    private function authorizeLookupForAidDistribution(): void
    {
        $user = Auth::user();

        if (
            $user?->can('view', AidDistribution::class) ||
            $user?->can('create', AidDistribution::class) ||
            $user?->can('update', AidDistribution::class)
        ) {
            return;
        }

        abort(403);
    }
}
