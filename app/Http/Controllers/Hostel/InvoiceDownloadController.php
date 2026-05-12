<?php

namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\InvoiceCalculator;
use Illuminate\Http\Response;

class InvoiceDownloadController extends Controller
{
    public function __invoke(Booking $booking, InvoiceCalculator $calculator): Response
    {
        $invoice = $calculator->calculate($booking);
        $ref = 'BK-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
        $lines = [
            'BIAM Foundation',
            'Bangladesh Institute of Admin & Mgmt',
            'Invoice No: INV-' . $ref,
            'Date: ' . now()->format('d/m/Y'),
            'Billed To: ' . ($booking->user?->name ?: '-'),
            'Guest Type: ' . ($booking->user?->role ?: '-'),
            'Booking Ref: ' . $ref,
            'Room: ' . ($booking->room?->room_number ?: '-') . ' (' . ucfirst(str_replace('_', '-', (string) $booking->room_type)) . ')',
            'Check-in: ' . ($booking->check_in_date?->toDateString() ?: '-'),
            'Check-out: ' . ($booking->check_out_date?->toDateString() ?: '-'),
            'Duration: ' . $invoice['duration'] . ' nights',
            'Room Rate: BDT ' . number_format($invoice['room_rate'], 2),
            'Rent Multiplier: ' . $invoice['rent_multiplier'] . 'x',
            'Subtotal: BDT ' . number_format($invoice['subtotal'], 2),
            'Meal Charges: BDT ' . number_format($invoice['meal_total'], 2),
            'Booking Money Paid: BDT ' . number_format($invoice['booking_money_paid'], 2),
            'Rent Paid: BDT ' . number_format($invoice['rent_paid'], 2),
            'Meal Paid: BDT ' . number_format($invoice['meal_paid'], 2),
            'Balance Due: BDT ' . number_format($invoice['balance_due'], 2),
            'Thank you for staying at BIAM Foundation',
        ];

        return response($this->pdfFromLines($lines), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice-' . $ref . '.pdf"',
        ]);
    }

    private function pdfFromLines(array $lines): string
    {
        $content = "BT\n/F1 12 Tf\n50 780 Td\n";

        foreach ($lines as $index => $line) {
            $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], (string) $line);
            $content .= ($index === 0 ? '' : "0 -24 Td\n") . "({$escaped}) Tj\n";
        }

        $content .= "ET";
        $objects = [];
        $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>";
        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[] = "<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $number => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($number + 1) . " 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        return $pdf . "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";
    }
}
