<?php

// Menentukan namespace lokasi controller
namespace App\Http\Controllers;

// Mengimpor Model Category
use App\Models\Category;

// Mengimpor Model Product
use App\Models\Product;

// Mengimpor Model StockTransaction
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\OpdSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Digunakan untuk query database mentah (raw query)
use Illuminate\Support\Facades\DB;

// Digunakan untuk tipe return berupa View
use Illuminate\View\View;

// Digunakan untuk manipulasi tanggal
use Carbon\Carbon;

// Controller untuk mengatur tampilan dashboard
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard
     */
    public function index(): View
    {
        // ============================
        // STATISTIK UTAMA (HARI INI)
        // ============================

        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $selectedDate = request('date') ? Carbon::parse(request('date')) : Carbon::today();
        $agg = DB::table('stock_transactions')
            ->select('product_id', DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END) as net'))
            ->whereDate('date', '<=', $selectedDate)
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');
        $productsBase = Product::select('id', 'name', 'min_stock', 'price')->get();
        $productsWithNet = $productsBase->map(function ($p) use ($agg) {
            $p->stock_on_date = (int)($agg[$p->id]->net ?? 0);
            return $p;
        });
        $criticalProducts = $productsWithNet->filter(fn ($p) => $p->stock_on_date <= $p->min_stock)->values();
        $lowStockCount = $criticalProducts->count();
        $totalStock = $productsWithNet->sum('stock_on_date');
        $totalInventoryValue = $productsWithNet->sum(function ($p) {
            return (int)($p->stock_on_date ?? 0) * (float)($p->price ?? 0);
        });
        $supplierCount = Supplier::count();
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $userDir = 'users/'.Auth::id().'/pinjam_pakai';
        $pinjamFiles = Storage::disk('local')->exists($userDir) ? Storage::disk('local')->files($userDir) : [];
        // ============================
        // DATA HARI INI
        // ============================
        $pinjamCount = count($pinjamFiles);
        $today = $selectedDate;
        $yesterday = (clone $selectedDate)->subDay();
        $inToday = StockTransaction::where('type', 'in')->whereDate('date', $today)->sum('quantity');
        $outToday = StockTransaction::where('type', 'out')->whereDate('date', $today)->sum('quantity');
        $valueInToday = StockTransaction::join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->where('stock_transactions.type', 'in')
            ->whereDate('stock_transactions.date', $today)
            ->sum(DB::raw('stock_transactions.quantity * products.price'));
        $valueOutToday = StockTransaction::join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->where('stock_transactions.type', 'out')
            ->whereDate('stock_transactions.date', $today)
            ->sum(DB::raw('stock_transactions.quantity * products.price'));
        $transactionsToday = StockTransaction::whereDate('date', $today)->count();
        $inYesterday = StockTransaction::where('type', 'in')->whereDate('date', $yesterday)->sum('quantity');
        $percentageChange = $inYesterday > 0 ? (($inToday - $inYesterday) / $inYesterday) * 100 : 0;
        $recentTransactions = StockTransaction::with(['product', 'user'])
            ->whereDate('date', $today)
            ->latest()
            ->take(10)
            ->get();

        // ============================
        // DATA UNTUK CHART (HARI INI PER JAM)
        // ============================

        $raw = StockTransaction::select(
                DB::raw('DATE_FORMAT(date, "%H") as hour'),
                DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_in'),
                DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_out')
            )
            ->whereDate('date', $today)
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get()
            ->keyBy('hour');
        $labels = collect(range(0, 23))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT).':00');
        $dataIn = $labels->map(function($label) use ($raw) {
            $h = substr($label, 0, 2);
            return (int)($raw[$h]->total_in ?? 0);
        });
        $dataOut = $labels->map(function($label) use ($raw) {
            $h = substr($label, 0, 2);
            return (int)($raw[$h]->total_out ?? 0);
        });



        return view('dashboard', compact(
            'totalProducts',        // Total produk
            'totalCategories',      // Total kategori
            'totalStock',
            'lowStockCount',
            'criticalProducts',
            'totalInventoryValue',
            'supplierCount',
            'opd',
            'pinjamCount',
            'percentageChange',
            'recentTransactions',
            'labels',
            'dataIn',
            'dataOut',
            'inToday',
            'outToday',
            'valueInToday',
            'valueOutToday',
            'transactionsToday'
        ));
}
}
