<?php

// Menentukan namespace lokasi controller ini
namespace App\Http\Controllers;

// Mengimpor Model Category untuk berinteraksi dengan tabel categories
use App\Models\Category;

// Digunakan untuk tipe return berupa RedirectResponse
use Illuminate\Http\RedirectResponse;

// Digunakan untuk menangkap data request dari form
use Illuminate\Http\Request;

// Digunakan untuk membuat slug otomatis dari string
use Illuminate\Support\Str;

// Digunakan untuk tipe return berupa View (tampilan)
use Illuminate\View\View;

// Controller untuk mengelola data kategori (CRUD)
class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori
     */
    public function index(): View
    {
        // Mengambil data kategori
        // withCount('products') → menghitung jumlah produk dalam setiap kategori
        // latest() → urut berdasarkan data terbaru
        // paginate(10) → menampilkan 10 data per halaman
        $categories = Category::withCount('products')->latest()->paginate(10);

        // Mengirim data ke view categories.index
        return view('categories.index', compact('categories'));
    }

    /**
     * Menampilkan halaman form tambah kategori
     */
    public function create(): View
    {
        // Menampilkan view untuk membuat kategori baru
        return view('categories.create');
    }

    /**
     * Menyimpan kategori baru ke database
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input dari form
        $validated = $request->validate([
            // name wajib diisi, berupa string, maksimal 255 karakter
            'name' => 'required|string|max:255',

            // description boleh kosong (nullable), jika diisi harus string
            'description' => 'nullable|string',
        ]);

        // Membuat slug dari name (contoh: "Makanan Ringan" → "makanan-ringan")
        $validated['slug'] = Str::slug($validated['name']);

        // Menyimpan data kategori ke database
        Category::create($validated);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('categories.index')
                         ->with('success', 'Category created successfully.');
    }

    /**
     * Menampilkan halaman edit kategori
     */
    public function edit(Category $category): View
    {
        // Route Model Binding:
        // Laravel otomatis mencari data category berdasarkan ID di URL
        return view('categories.edit', compact('category'));
    }

    /**
     * Menampilkan detail kategori
     */
    public function show(Category $category): View
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Mengupdate data kategori di database
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Jika nama kategori berubah
        if ($category->name !== $validated['name']) {

            // Maka slug dibuat ulang berdasarkan nama baru
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Update data kategori dengan data yang sudah divalidasi
        $category->update($validated);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('categories.index')
                         ->with('success', 'Category updated successfully.');
    }

    /**
     * Menghapus kategori dari database
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Menghapus data kategori
        $category->delete();

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
    
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = (array) $request->input('ids', []);
        $ids = array_values(array_filter($ids, fn($v) => is_numeric($v)));
        if (empty($ids)) {
            return redirect()->route('categories.index')
                             ->withErrors(['delete' => 'Tidak ada jenis belanja yang dipilih.']);
        }
        Category::whereIn('id', $ids)->delete();
        return redirect()->route('categories.index')
                         ->with('success', 'Jenis belanja terpilih dihapus.');
    }
}
