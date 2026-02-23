{{-- Partial tombol aksi standar: Lihat, Edit, Hapus --}}
{{-- Gunakan: @include('partials.action_buttons', ['show' => route(...), 'edit' => route(...), 'delete' => route(...)]) --}}
<a href="{{ $show ?? '#' }}" class="action-btn view">
    <i class="fas fa-eye"></i> Lihat
</a>
<a href="{{ $edit ?? '#' }}" class="action-btn edit">
    <i class="fas fa-edit"></i> Edit
</a>
<form method="POST" action="{{ $delete ?? '#' }}" class="inline" onsubmit="return confirm('Hapus item ini?')">
    @csrf
    <button type="submit" class="action-btn delete">
        <i class="fas fa-trash"></i> Hapus
    </button>
</form>
