<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Record stock out (sale or service sparepart).
     *
     * @param string $productCode
     * @param int $quantity
     * @param string|null $referenceType
     * @param string|null $referenceCode
     * @param string|null $notes
     */
    public function stockOut(string $productCode, int $quantity, string $referenceType = null, string $referenceCode = null, string $notes = null): void
    {
        $product = Product::findOrFail($productCode);
        $previousStock = $product->stock;

        if ($product->stock < $quantity) {
            throw new \Exception("Insufficient stock for product {$product->name}. Available: {$product->stock}, Requested: {$quantity}");
        }

        $product->stock -= $quantity;
        $product->save();

        StockMovement::create([
            'product_code'   => $productCode,
            'created_by'     => Auth::id(),
            'type'           => 'out',
            'total_stock'    => $quantity,
            'previous_stock' => $previousStock,
            'current_stock'  => $product->stock,
            'movement_date'  => now(),
            'reference_type' => $referenceType,
            'reference_code' => $referenceCode,
            'notes'          => $notes,
        ]);
    }

    /**
     * Record stock in (procurement received).
     *
     * @param string $productCode
     * @param int $quantity
     * @param string|null $referenceType
     * @param string|null $referenceCode
     * @param string|null $notes
     */
    public function stockIn(string $productCode, int $quantity, string $referenceType = null, string $referenceCode = null, string $notes = null): void
    {
        $product = Product::findOrFail($productCode);
        $previousStock = $product->stock;

        $product->stock += $quantity;
        $product->save();

        StockMovement::create([
            'product_code'   => $productCode,
            'created_by'     => Auth::id(),
            'type'           => 'in',
            'total_stock'    => $quantity,
            'previous_stock' => $previousStock,
            'current_stock'  => $product->stock,
            'movement_date'  => now(),
            'reference_type' => $referenceType,
            'reference_code' => $referenceCode,
            'notes'          => $notes,
        ]);
    }

    /**
     * Manual stock adjustment by owner.
     *
     * @param string $productCode
     * @param int $newStock
     * @param string|null $notes
     */
    public function adjust(string $productCode, int $newStock, string $notes = null): void
    {
        $product = Product::findOrFail($productCode);
        $previousStock = $product->stock;
        $quantity = abs($newStock - $previousStock);

        $product->stock = $newStock;
        $product->save();

        StockMovement::create([
            'product_code'   => $productCode,
            'created_by'     => Auth::id(),
            'type'           => 'adjustment',
            'total_stock'    => $quantity,
            'previous_stock' => $previousStock,
            'current_stock'  => $newStock,
            'movement_date'  => now(),
            'notes'          => $notes ?? 'Manual stock adjustment',
        ]);
    }
}
