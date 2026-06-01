<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Message;
use App\Notifications\NewMessageNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Booking $order): RedirectResponse
    {
        $user = auth()->user();
        abort_unless(
            $order->client_id === $user->id || $order->master_id === $user->id,
            403
        );

        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ], [
            'body.required' => 'Повідомлення не може бути порожнім.',
        ]);

        Message::create([
            'booking_id' => $order->id,
            'sender_id' => $user->id,
            'body' => $request->body,
        ]);

        $other = $order->client_id === $user->id ? $order->master : $order->client;
        $other->notify(new NewMessageNotification($order));

        $redirectRoute = $user->isMaster()
            ? route('cabinet.orders.show', $order)
            : route('account.orders.show', $order);

        return redirect($redirectRoute.'#chat')->with('success', 'Повідомлення надіслано.');
    }

    public function poll(Booking $order): View
    {
        $user = auth()->user();
        abort_unless(
            $order->client_id === $user->id || $order->master_id === $user->id,
            403
        );

        $messages = $order->messages()->with('sender')->oldest()->get();

        return view('partials.messages-list', compact('messages'));
    }
}
