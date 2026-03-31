<?php

namespace App\Events;

use App\Models\Stock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Event ini dikirim setiap kali stok berubah agar frontend / dashboard
    // bisa menerima update realtime.
    public function __construct(public Stock $stock)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('company.' . $this->stock->company_id)];
    }

    public function broadcastAs(): string
    {
        return 'stock.updated';
    }
}
