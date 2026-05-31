<?php

namespace App\Http\Controllers\Cabinet;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\WorkReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkReportController extends Controller
{
    public function store(Request $request, Booking $order): RedirectResponse
    {
        abort_unless($order->master_id === auth()->id(), 403);
        abort_unless($order->status === OrderStatus::Completed, 403);

        $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ], [
            'content.required' => 'Звіт не може бути порожнім.',
        ]);

        WorkReport::updateOrCreate(
            ['booking_id' => $order->id],
            [
                'master_id' => auth()->id(),
                'client_id' => $order->client_id,
                'content' => $request->content,
            ]
        );

        return redirect()->route('cabinet.orders.show', $order)->with('success', 'Звіт збережено.');
    }
}
