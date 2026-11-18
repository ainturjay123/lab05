<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryTransactionObserver
{
    public function created(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = $transaction->inventoryProduct()
                ->lockForUpdate()
                ->firstOrFail();

            if ($transaction->type === 'stock_in') {
                $product->increment('quantity', $transaction->quantity);
            } elseif ($transaction->type === 'stock_out') {
                if ($product->quantity < $transaction->quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Not enough stock available.',
                    ]);
                }

                $product->decrement('quantity', $transaction->quantity);
            } else {
                throw ValidationException::withMessages([
                    'type' => 'Transaction type must be stock_in or stock_out.',
                ]);
            }
        });
    }
}
