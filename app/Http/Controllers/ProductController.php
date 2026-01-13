<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return view('inventory');
    }
    public function apiIndex()
    {
        return Product::all();
    }


    public function store(Request $r) {
        return Product::create($r->validate([
            'name'=>'required',
            'stock'=>'required|integer|min:0',
            'price'=>'required|integer|min:0'
        ]));
    }

    public function sell(Request $r, Product $product)
    {
        $data = $r->validate([
            'qty' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($product, $data) {

            if ($product->stock < $data['qty']) {
                return response()->json([
                    'message' => 'Stok tidak cukup'
                ], 422);
            }

            $product->decrement('stock', $data['qty']);

            return $product->fresh();
        });
    }
}
