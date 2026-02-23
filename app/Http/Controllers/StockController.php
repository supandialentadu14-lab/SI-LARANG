<?php

// Menentukan namespace controller
namespace App\Http\Controllers;

// Mengimpor model Product
use App\Models\Product;

// Mengimpor model StockTransaction
use App\Models\StockTransaction;

// Digunakan untuk tipe return redirect
use App\Models\Transaction;

// Digunakan untuk menangkap request dari form
use Illuminate\Http\RedirectResponse;

// Digunakan untuk transaksi database (agar aman & konsisten)
use Illuminate\Http\Request;

// Digunakan untuk tipe return View
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

// Controller untuk mengelola transaksi stok
class StockController extends Controller
{
    /**
     * Menampilkan daftar transaksi stok
     */
    public function index(): View
    {
        // Mengambil transaksi stok beserta relasi product dan user
        $transactions = StockTransaction::with(['product', 'user'])
            ->latest()        // Urutkan dari terbaru
            ->paginate(20);   // Tampilkan 20 data per halaman

        // Ambil semua produk (hanya kolom stock dan price)
        $products = Product::select('stock', 'price')->get();

        // Menghitung total saldo akhir (total stok semua produk)
        $totalSaldoAkhir = $products->sum('stock');

        // Menghitung total nilai persediaan (stok × harga)
        $grandTotal = $products->sum(function ($product) {
            return ($product->stock ?? 0) * ($product->price ?? 0);
        });

        // Kirim data ke view
        return view('stock.index', compact(
            'transactions',
            'totalSaldoAkhir',
            'grandTotal'
        ));
    }

    /**
     * Menampilkan form tambah transaksi stok
     */
    public function create()
    {
        // Mengambil semua produk beserta total stok masuk
        $products = Product::withSum(['transactions as stock_in' => function ($q) {
                $q->where('type', 'in'); // Filter transaksi masuk
            }], 'quantity')

            // Mengambil total stok keluar
            ->withSum(['transactions as stock_out' => function ($q) {
                $q->where('type', 'out'); // Filter transaksi keluar
            }], 'quantity')

            ->get();

        // Menghitung stok aktual berdasarkan transaksi (masuk - keluar)
        foreach ($products as $product) {
            $product->calculated_stock =
                ($product->stock_in ?? 0) - ($product->stock_out ?? 0);
        }

        // Kirim data ke view
        return view('stock.create', compact('products'));
    }

    /**
     * Menyimpan transaksi stok baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'product_id' => 'required|exists:products,id', // Produk harus ada
            'type' => 'required|in:in,out', // Hanya boleh "in" atau "out"
            'quantity' => 'required|integer|min:1', // Jumlah minimal 1
            'date' => 'required|date', // Tanggal wajib valid
        ]);

        // Ambil produk berdasarkan ID
        $product = Product::findOrFail($request->product_id);

        // Jika tipe transaksi adalah stok masuk
        if ($request->type === 'in') {

            // Tambahkan stok produk
            $product->increment('stock', $request->quantity);

        } else {

            // Jika stok keluar, cek apakah stok mencukupi
            if ($product->stock < $request->quantity) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }

            // Kurangi stok produk
            $product->decrement('stock', $request->quantity);
        }

        // Simpan transaksi stok ke database
        StockTransaction::create([
            'product_id' => $product->id,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'date' => $request->date,
            'nosur' => $request->nosur,
            'notes' => $request->notes,
            'user_id' => auth()->id(), // Simpan ID user yang login
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('stock.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function update(Request $request, $id)
{
    $transaction = Transaction::findOrFail($id);

    // ===============================
    // VALIDASI DATA
    // ===============================
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'date'       => 'required|date',
        'type'       => 'required|in:in,out',
        'quantity'   => 'required|integer|min:1',
        'nosur'      => 'nullable|string|max:255',
        'notes'      => 'nullable|string'
    ]);

    // ===============================
    // CEK STOK JIKA TRANSAKSI KELUAR
    // ===============================

    if ($request->type === 'out') {

        // Hitung total stok produk saat ini TANPA transaksi lama
        $totalMasuk = Transaction::where('product_id', $request->product_id)
            ->where('type', 'in')
            ->where('id', '!=', $transaction->id)
            ->sum('quantity');

        $totalKeluar = Transaction::where('product_id', $request->product_id)
            ->where('type', 'out')
            ->where('id', '!=', $transaction->id)
            ->sum('quantity');

        $stokSaatIni = $totalMasuk - $totalKeluar;

        if ($request->quantity > $stokSaatIni) {
            return back()->withErrors([
                'quantity' => 'Stok tidak mencukupi untuk transaksi keluar ini.'
            ])->withInput();
        }
    }

    // ===============================
    // UPDATE DATA TRANSAKSI
    // ===============================
    $transaction->update([
        'product_id' => $request->product_id,
        'date'       => $request->date,
        'type'       => $request->type,
        'quantity'   => $request->quantity,
        'nosur'      => $request->nosur,
        'notes'      => $request->notes,
    ]);

    return redirect()->route('stock.index')
        ->with('success', 'Transaksi berhasil diperbarui.');
}

    public function edit($id)
{
    $transaction = Transaction::findOrFail($id);
    $products = Product::all();

    return view('stock.edit', compact('transaction', 'products'));
}


    /**
     * Menghapus transaksi stok
     */
    public function destroy(StockTransaction $transaction): RedirectResponse
    {
        $product = $transaction->product;
        if ($transaction->type === 'in' && $product->stock < $transaction->quantity) {
            return redirect()->route('stock.index')
                ->withErrors(['delete' => 'Tidak bisa menghapus transaksi masuk karena stok saat ini lebih kecil dari jumlah transaksi.']);
        }

        DB::transaction(function () use ($transaction, $product) {
            if ($transaction->type === 'in') {
                $product->decrement('stock', $transaction->quantity);
            } else {
                $product->increment('stock', $transaction->quantity);
            }
            $transaction->delete();
        });

        return redirect()->route('stock.index')
            ->with('success', 'Transaksi berhasil dihapus dan stok telah disesuaikan.');
    }
}
