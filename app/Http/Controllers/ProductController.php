<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        // Ambil barang terakhir berdasarkan id
        $lastProduct = Product::latest()->first();

        if ($lastProduct) {
            $lastNumber = (int) substr($lastProduct->sku, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $newSku = 'BRG-'.str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return view('products.create', compact('categories', 'suppliers', 'newSku'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function index(Request $request)
    {
        $products = Product::with(['category', 'transactions'])
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->paginate(10);

        return view('products.index', compact('products'));
    }
    /**
     * SIMPAN PRODUK
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'price' => 'required',
        'unit' => 'required',
        'category_id' => 'required',
    ]);

    $lastProduct = Product::latest()->first();

    if ($lastProduct) {
        $lastNumber = (int) substr($lastProduct->sku, 4);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
$slug = Str::slug($request->name);

    $newSku = 'BRG-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

    Product::create([
    'name' => $request->name,
    'slug' => $slug, // TAMBAHKAN INI
    'sku' => $newSku,
    'price' => $request->price,
    'unit' => $request->unit,
    'category_id' => $request->category_id,
    'supplier_id' => $request->supplier_id,
    'description' => $request->description,
]);


    return redirect()->route('products.index')->with('success', 'Barang berhasil ditambahkan');
}


    /**
     * UPDATE PRODUK
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'unit' => 'required',
            'category_id' => 'required',
            'supplier_id' => 'required',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'unit' => $request->unit, // ✅ FIX DI SINI
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'description' => $request->description,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * REPORT KARTU
     */
    public function kartu(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $transactions = StockTransaction::with('product')
            ->when($startDate, fn ($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('date', '<=', $endDate))
            ->orderBy('date', 'asc')
            ->get();

        $reportData = [];

        foreach ($transactions as $trx) {
            $reportData[] = [
                'date' => $trx->date,
                'reference' => $trx->reference_number ?? '-',
                'uraian' => $trx->description ?? $trx->product->name,
                'name' => $trx->product->name,
                'unit' => $trx->product->unit, // ✅ TAMBAH AGAR SATUAN SESUAI DATABASE
                'masuk' => $trx->type === 'in' ? $trx->quantity : 0,
                'keluar' => $trx->type === 'out' ? $trx->quantity : 0,
                'harga' => $trx->price ?? $trx->product->price,
                'keterangan' => $trx->notes ?? '',
            ];
        }

        return view('reports.kartu', compact(
            'reportData',
            'startDate',
            'endDate'
        ));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
    
    public function bulkDestroy(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        $ids = array_values(array_filter($ids, fn($v) => is_numeric($v)));
        if (empty($ids)) {
            return redirect()->route('products.index')->withErrors(['delete' => 'Tidak ada barang yang dipilih.']);
        }
        Product::whereIn('id', $ids)->delete();
        return redirect()->route('products.index')->with('success', 'Barang terpilih berhasil dihapus.');
    }
}
