<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('car')->latest('id');

        $dari = $request->input('dari');
        $sampai = $request->input('sampai');
        $status = $request->input('status', '');

        if ($dari) {
            $query->whereDate('tanggal_mulai', '>=', Carbon::parse($dari));
        }

        if ($sampai) {
            $query->whereDate('tanggal_mulai', '<=', Carbon::parse($sampai));
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->get();

        $totalBooking = $bookings->count();
        $totalHari = $bookings->sum('jumlah_hari');
        $totalPendapatan = $bookings->whereIn('status', ['selesai', 'dikonfirmasi'])
            ->sum('total_harga');
        $totalNilaiBooking = $bookings->whereNotIn('status', ['dibatalkan'])
            ->sum('total_harga');

        return view('reports.index', compact(
            'bookings',
            'dari',
            'sampai',
            'status',
            'totalBooking',
            'totalHari',
            'totalPendapatan',
            'totalNilaiBooking',
        ));
    }

    public function export(Request $request)
    {
        $query = Booking::with('car')->latest('id');

        $dari = $request->input('dari');
        $sampai = $request->input('sampai');
        $status = $request->input('status', '');

        if ($dari) {
            $query->whereDate('tanggal_mulai', '>=', Carbon::parse($dari));
        }

        if ($sampai) {
            $query->whereDate('tanggal_mulai', '<=', Carbon::parse($sampai));
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->get();

        $filename = 'laporan-booking-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = [
            'No',
            'Penyewa',
            'Telepon',
            'Mobil',
            'Plat Nomor',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Jumlah Hari',
            'Harga/Hari',
            'Total Harga',
            'Status',
        ];

        return response()->streamDownload(function () use ($bookings, $columns) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, $columns);

            foreach ($bookings as $i => $booking) {
                fputcsv($handle, [
                    $i + 1,
                    $booking->nama_penyewa,
                    $booking->no_telepon,
                    $booking->car->nama_mobil,
                    $booking->car->plat_nomor,
                    $booking->tanggal_mulai->format('d-m-Y'),
                    $booking->tanggal_selesai->format('d-m-Y'),
                    $booking->jumlah_hari,
                    number_format($booking->harga_per_hari, 0, ',', '.'),
                    number_format($booking->total_harga, 0, ',', '.'),
                    $booking->status,
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }
}