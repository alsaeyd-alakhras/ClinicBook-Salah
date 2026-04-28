<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\BookingsExport;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly BookingService $bookingService) {}

    public function index(Request $request)
    {
        $this->authorize('view', Booking::class);

        if ($request->ajax()) {
            $query = $this->filteredQuery($request);

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('booking_date', fn (Booking $booking) => optional($booking->booking_date)->format('Y-m-d'))
                ->editColumn('created_at', fn (Booking $booking) => optional($booking->created_at)->format('Y-m-d H:i'))
                ->addColumn('visit_type_label', fn (Booking $booking) => $this->bookingService->visitTypeLabel($booking->visit_type))
                ->addColumn('status_label', fn (Booking $booking) => $booking->status === 'ticket_received' ? 'تم الاستلام' : 'قيد الانتظار')
                ->addColumn('actions', fn (Booking $booking) => $booking->id)
                ->rawColumns(['actions'])
                ->make(true);
        }

        $activeDate = $this->bookingService->getActiveBookingDate();

        return view('dashboard.bookings.index', [
            'defaultDate' => $activeDate?->format('Y-m-d'),
        ]);
    }

    public function confirm(Request $request, int $id)
    {
        $this->authorize('update', Booking::class);

        $booking = Booking::query()->findOrFail($id);
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'ticket_received']);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('dashboard.bookings.index')->with('success', 'تم تحديث حالة الحجز.');
    }

    public function export(Request $request)
    {
        $this->authorize('view', Booking::class);

        $rows = $this->filteredQuery($request)->orderBy('booking_date')->orderBy('serial_number')->get();
        $type = $request->query('type', 'excel');

        if ($type === 'pdf') {
            $pdf = PDF::loadView('dashboard.reports.bookings', [
                'rows' => $rows,
                'filters' => [
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'patient_name' => $request->patient_name,
                    'visit_type' => $request->visit_type,
                    'status' => $request->status,
                ],
            ], [], [
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'default_font_size' => 11,
                'default_font' => 'Arial',
            ]);

            return $pdf->stream('تقرير_الحجوزات_'.now()->format('Ymd_His').'.pdf');
        }

        return Excel::download(
            new BookingsExport($rows),
            'حجوزات_العيادة_'.now()->format('Ymd_His').'.xlsx'
        );
    }

    private function filteredQuery(Request $request)
    {
        $query = Booking::query();

        if ($request->filled('from_date')) {
            $query->whereDate('booking_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('booking_date', '<=', $request->to_date);
        }

        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', '%'.$request->patient_name.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('visit_type')) {
            $query->where('visit_type', $request->visit_type);
        }

        if (! $request->filled('from_date') && ! $request->filled('to_date')) {
            $activeDate = $this->bookingService->getActiveBookingDate();
            if ($activeDate) {
                $query->whereDate('booking_date', $activeDate->toDateString());
            }
        }

        return $query->orderByDesc('booking_date')->orderBy('serial_number');
    }
}
