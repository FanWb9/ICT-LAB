<div class="p-4 space-y-4">
    <div class="flex justify-center items-center">
        @if ($record->image)
            <img src="{{ $record->image }}" alt="Foto Siswa" class="max-w-xs h-auto">
        @else
            <p>Gambar tidak tersedia</p>
        @endif
    </div>
</div>
