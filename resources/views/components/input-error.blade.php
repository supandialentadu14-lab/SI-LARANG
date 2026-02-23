{{-- Mendefinisikan properti yang diterima oleh komponen, yaitu 'messages' --}}
@props(['messages'])

{{-- Mengecek apakah ada pesan error --}}
@if ($messages)

    {{-- 
        Menampilkan daftar pesan dalam bentuk list (ul).
        $attributes->merge() digunakan untuk menggabungkan class tambahan
        jika komponen dipanggil dengan atribut lain.
    --}}
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>

        {{-- 
            Mengubah $messages menjadi array (jika belum),
            lalu melakukan perulangan untuk setiap pesan.
        --}}
        @foreach ((array) $messages as $message)

            {{-- Menampilkan setiap pesan dalam bentuk list item --}}
            <li>{{ $message }}</li>

        @endforeach

    </ul>

@endif
