<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BelanjaModalController;
use App\Http\Controllers\NotaPesananController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\KwitansiController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\PinjamPakaiController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout.get');
    // Delete
    Route::delete('/stock/{transaction}', [StockController::class, 'destroy'])
        ->name('stock.destroy');

    // Categories
    Route::resource('categories', CategoryController::class);
    Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk_delete');

    // Stock
    Route::resource('stock', StockController::class);

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Products
    Route::resource('products', ProductController::class);
    Route::post('products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('products.bulk_delete');

    // Stock Management
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/create', [StockController::class, 'create'])->name('stock.create');
    Route::post('stock', [StockController::class, 'store'])->name('stock.store');

    // Report biasa (yang lama)
    Route::get('reports',
        [ReportController::class, 'index']
    )->name('reports.index');

    // Report kartu per item (yang baru seperti gambar)
    // Report kartu tahunan (jika dipakai)
    Route::get('reports/kartu.tahunan',
        [ReportController::class, 'kartuTahunan']
    )->name('reports.kartu.tahunan');

    // Berita Acara Pinjam Pakai
    Route::get('reports/berita-pinjam-pakai', [PinjamPakaiController::class, 'form'])->name('reports.pinjam.form');
    Route::post('reports/berita-pinjam-pakai', [PinjamPakaiController::class, 'report'])->name('reports.pinjam.report');
    Route::post('reports/berita-pinjam-pakai/save', [PinjamPakaiController::class, 'save'])->name('reports.pinjam.save');
    Route::get('reports/berita-pinjam-pakai/{id}/edit', [PinjamPakaiController::class, 'edit'])->name('reports.pinjam.edit');
    Route::get('reports/berita-pinjam-pakai/list', [PinjamPakaiController::class, 'list'])->name('reports.pinjam.list');
    Route::get('reports/berita-pinjam-pakai/{id}', [PinjamPakaiController::class, 'show'])->name('reports.pinjam.show');
    Route::post('reports/berita-pinjam-pakai/{id}/delete', [PinjamPakaiController::class, 'delete'])->name('reports.pinjam.delete');
    Route::get('reports/berita-pinjam-pakai/{id}/delete', function () {
        return redirect()->route('reports.pinjam.list');
    });

    // Berita Acara Stock Opname Persediaan
    Route::get('reports/berita-opname', [OpnameController::class, 'form'])->name('reports.opname.form');
    Route::get('reports/berita-opname/prefill', [OpnameController::class, 'prefill'])->name('reports.opname.prefill');
    Route::post('reports/berita-opname', [OpnameController::class, 'report'])->name('reports.opname.report');
    Route::post('reports/berita-opname/save', [OpnameController::class, 'save'])->name('reports.opname.save');
    Route::get('reports/berita-opname/{id}/edit', [OpnameController::class, 'edit'])->name('reports.opname.edit');
    Route::get('reports/berita-opname/list', [OpnameController::class, 'list'])->name('reports.opname.list');
    Route::get('reports/berita-opname/{id}', [OpnameController::class, 'show'])->name('reports.opname.show');
    Route::post('reports/berita-opname/{id}/delete', [OpnameController::class, 'delete'])->name('reports.opname.delete');
    
    // Belanja Modal
    Route::get('reports/belanja-modal', [BelanjaModalController::class, 'form'])->name('reports.belanja.modal.form');
    Route::post('reports/belanja-modal', [BelanjaModalController::class, 'report'])->name('reports.belanja.modal.report');
    Route::post('reports/belanja-modal/save', [BelanjaModalController::class, 'save'])->name('reports.belanja.modal.save');
    Route::get('reports/belanja-modal/save', function () {
        return redirect()->route('reports.belanja.modal.list');
    });
    Route::get('reports/belanja-modal/list', [BelanjaModalController::class, 'index'])->name('reports.belanja.modal.list');
    Route::get('reports/belanja-modal/preview-all', [BelanjaModalController::class, 'previewAll'])->name('reports.belanja.modal.preview_all');
    Route::get('reports/belanja-modal/{id}', [BelanjaModalController::class, 'show'])->name('reports.belanja.modal.show');
    Route::get('reports/belanja-modal/{id}/edit', [BelanjaModalController::class, 'edit'])->name('reports.belanja.modal.edit');
    Route::post('reports/belanja-modal/{id}/delete', [BelanjaModalController::class, 'delete'])->name('reports.belanja.modal.delete');
    
    // Nota Pesanan
    Route::get('reports/nota-pesanan', [NotaPesananController::class, 'form'])->name('reports.nota.form');
    Route::post('reports/nota-pesanan', [NotaPesananController::class, 'report'])->name('reports.nota.report');
    Route::post('reports/nota-pesanan/save', [NotaPesananController::class, 'save'])->name('reports.nota.save');
    Route::get('reports/nota-pesanan/list', [NotaPesananController::class, 'list'])->name('reports.nota.list');
    Route::get('reports/nota-pesanan/{id}', [NotaPesananController::class, 'show'])->name('reports.nota.show');
    Route::get('reports/nota-pesanan/{id}/edit', [NotaPesananController::class, 'edit'])->name('reports.nota.edit');
    Route::post('reports/nota-pesanan/{id}/update', [NotaPesananController::class, 'update'])->name('reports.nota.update');
    Route::post('reports/nota-pesanan/{id}/delete', [NotaPesananController::class, 'delete'])->name('reports.nota.delete');

    // Berita Acara Pemeriksaan Barang/Pekerjaan (berdasarkan Nota Pesanan)
    Route::get('reports/berita-pemeriksaan', [PemeriksaanController::class, 'form'])->name('reports.pemeriksaan.form');
    Route::post('reports/berita-pemeriksaan', [PemeriksaanController::class, 'report'])->name('reports.pemeriksaan.report');
    Route::post('reports/berita-pemeriksaan/save', [PemeriksaanController::class, 'save'])->name('reports.pemeriksaan.save');
    Route::get('reports/berita-pemeriksaan/list', [PemeriksaanController::class, 'list'])->name('reports.pemeriksaan.list');
    Route::get('reports/berita-pemeriksaan/{id}', [PemeriksaanController::class, 'show'])->name('reports.pemeriksaan.show');
    Route::get('reports/berita-pemeriksaan/{id}/edit', [PemeriksaanController::class, 'edit'])->name('reports.pemeriksaan.edit');
    Route::post('reports/berita-pemeriksaan/{id}/delete', [PemeriksaanController::class, 'delete'])->name('reports.pemeriksaan.delete');
    
    Route::get('reports/berita-penerimaan', [PenerimaanController::class, 'form'])->name('reports.penerimaan.form');
    Route::post('reports/berita-penerimaan', [PenerimaanController::class, 'report'])->name('reports.penerimaan.report');
    Route::post('reports/berita-penerimaan/save', [PenerimaanController::class, 'save'])->name('reports.penerimaan.save');
    Route::get('reports/berita-penerimaan/list', [PenerimaanController::class, 'list'])->name('reports.penerimaan.list');
    Route::get('reports/berita-penerimaan/{id}/edit', [PenerimaanController::class, 'edit'])->name('reports.penerimaan.edit');
    Route::get('reports/berita-penerimaan/{id}', [PenerimaanController::class, 'show'])->name('reports.penerimaan.show');
    Route::post('reports/berita-penerimaan/{id}/delete', [PenerimaanController::class, 'delete'])->name('reports.penerimaan.delete');
    
    Route::get('reports/kwitansi', [KwitansiController::class, 'form'])->name('reports.kwitansi.form');
    Route::post('reports/kwitansi', [KwitansiController::class, 'report'])->name('reports.kwitansi.report');
    Route::post('reports/kwitansi/save', [KwitansiController::class, 'save'])->name('reports.kwitansi.save');
    Route::get('reports/kwitansi/print-all', [KwitansiController::class, 'printAll'])->name('reports.kwitansi.print_all');
    Route::get('reports/kwitansi/list', [KwitansiController::class, 'list'])->name('reports.kwitansi.list');
    Route::get('reports/kwitansi/{id}/show', [KwitansiController::class, 'show'])->name('reports.kwitansi.show');
    Route::get('reports/kwitansi/{id}/edit', [KwitansiController::class, 'edit'])->name('reports.kwitansi.edit');
    Route::post('reports/kwitansi/{id}/update', [KwitansiController::class, 'update'])->name('reports.kwitansi.update');
    Route::post('reports/kwitansi/{id}/delete', [KwitansiController::class, 'delete'])->name('reports.kwitansi.delete');
    Route::get('settings/opd', [OpdController::class, 'edit'])->name('settings.opd.edit');
    Route::post('settings/opd', [OpdController::class, 'update'])->name('settings.opd.update');
    Route::get('settings/opd/list', [OpdController::class, 'index'])->name('settings.opd.index');
    
    // Settings: Master data pihak-pihak pengadaan untuk Nota Pesanan
    Route::get('settings/nota-master', [ReportController::class, 'notaMasterForm'])->name('settings.nota.master.edit');
    Route::post('settings/nota-master', [ReportController::class, 'notaMasterSave'])->name('settings.nota.master.update');
    Route::get('settings/nota-master/list', [ReportController::class, 'notaMasterList'])->name('settings.nota.master.list');

    Route::get('profile', [UserController::class, 'editSelf'])->name('profile.edit');

    // User Management (Admin only)
    Route::middleware('can:admin-access')->group(function () {
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
