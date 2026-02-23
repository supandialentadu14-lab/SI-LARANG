{{-- Mendefinisikan properti yang diterima oleh komponen, yaitu 'status' --}}
@props(['status'])

{{-- Mengecek apakah variabel $status memiliki nilai --}}
@if ($status)

    {{-- 
        Menampilkan div jika status ada.
        $attributes->merge() digunakan untuk menggabungkan class tambahan 
        jika komponen ini dipanggil dengan atribut lain.
    --}}
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        
        {{-- Menampilkan isi pesan status --}}
        {{ $status }}
        
    </div>

@endif
