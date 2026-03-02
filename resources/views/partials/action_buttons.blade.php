{{-- Partial tombol aksi dropdown: Lihat, Edit, Hapus --}}
<div x-data="{ 
        open: false,
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    this.reposition();
                });
            }
        },
        reposition() {
            if (!this.open) return;
            const btn = this.$refs.btn.getBoundingClientRect();
            const menuWidth = 144; 
            let left = btn.right;
            let top = btn.bottom;
            
            if (left + menuWidth > window.innerWidth) {
                left = btn.right - menuWidth;
            } else {
                left = btn.right - menuWidth; 
            }

            this.$refs.panel.style.top = `${top}px`;
            this.$refs.panel.style.left = `${left}px`;
        }
    }" 
    class="relative inline-block text-left"
    @scroll.window="open = false"
    @resize.window="open = false"
>
    <button x-ref="btn" @click.stop="toggle()" type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none p-2 rounded-full hover:bg-gray-100 transition">
        <i class="fas fa-ellipsis-v"></i>
    </button>

    <template x-teleport="body">
        <div x-ref="panel"
             x-show="open" 
             @click.outside="open = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             style="display: none; position: fixed; z-index: 9999999; width: 9rem;"
             class="mt-1 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        >
            <div class="py-1">
                @if(isset($show))
                <a href="{{ $show }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                    <i class="fas fa-eye w-5 mr-2 text-gray-400 group-hover:text-indigo-500"></i> Lihat
                </a>
                @endif
                <a href="{{ $edit ?? '#' }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                    <i class="fas fa-edit w-5 mr-2 text-gray-400 group-hover:text-indigo-500"></i> Edit
                </a>
                <form method="POST" action="{{ $delete ?? '#' }}" class="block">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="if(confirm('Hapus item ini?')) $el.closest('form').submit()" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition">
                        <i class="fas fa-trash w-5 mr-2 text-red-400 group-hover:text-red-500"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </template>
</div>
