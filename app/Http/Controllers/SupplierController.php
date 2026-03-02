<?php

// Menentukan namespace controller
namespace App\Http\Controllers;

// Mengimpor model Supplier
use App\Models\Supplier;

// Digunakan untuk tipe return redirect
use Illuminate\Http\RedirectResponse;

// Digunakan untuk menangkap data request dari form
use Illuminate\Http\Request;

// Digunakan untuk tipe return View (tampilan)
use Illuminate\View\View;

// Controller untuk mengelola data supplier (CRUD)
class SupplierController extends Controller
{
    /**
     * Menampilkan daftar supplier
     */
    public function index(): View
    {
        // Mengambil data supplier dari database
        // latest() → urut berdasarkan data terbaru
        // paginate(10) → tampilkan 10 data per halaman
        $suppliers = Supplier::latest()->paginate(10);

        // Mengirim data supplier ke view index
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Menampilkan form tambah supplier
     */
    public function create(): View
    {
        // Menampilkan halaman form create supplier
        return view('suppliers.create');
    }

    /**
     * Menyimpan supplier baru ke database
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input dari form
        $validated = $request->validate([
            // Nama perushaan wajib diisi, string, maksimal 255 karakter
            'name' => 'required|string|max:255',

            // Nama direktur wajib diisi, string, maksimal 255 karakter
            'dir' => 'required|string|max:255',

            // Email boleh kosong, jika diisi harus format email
            'email' => 'nullable|email|max:255',

            // Nomor telepon boleh kosong, maksimal 20 karakter
            'phone' => 'nullable|string|max:20',

            // Alamat boleh kosong
            'address' => 'nullable|string',
        ]);

        // Simpan data supplier ke database
        Supplier::create($validated);

        // Redirect ke halaman daftar supplier dengan pesan sukses
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Menampilkan form edit supplier
     */
    public function edit(Supplier $supplier): View
    {
        // Route Model Binding:
        // Laravel otomatis mencari supplier berdasarkan ID di URL
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Menampilkan detail supplier
     */
    public function show(Supplier $supplier): View
    {
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Mengupdate data supplier
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        // Validasi ulang data sebelum update
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dir' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        // Update data supplier berdasarkan input yang sudah divalidasi
        $supplier->update($validated);

        // Redirect kembali ke halaman daftar supplier dengan pesan sukses
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Menghapus supplier dari database
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        // Menghapus data supplier
        $supplier->delete();

        // Redirect kembali ke halaman daftar supplier dengan pesan sukses
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
