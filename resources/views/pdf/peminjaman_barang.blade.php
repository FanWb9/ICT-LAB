<!DOCTYPE html>
<html>
<head>
    <title>Peminjaman Barang PDF</title>
    <style>
        body { font-family: sans-serif; }
        .title { font-size: 24px; margin-bottom: 20px; }
        .details { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="title text-center">Detail Peminjaman Barang</div>
        
        <div class="row">
          
            <div class="">
                @if ($record->image)
                    <img src="{{ $record->image }}" alt="Foto Siswa" class="img-fluid" style="max-width: 350px; height: auto;">
                @else
                    <p>Gambar tidak tersedia</p>
                @endif
            </div>
            <div class="">
                <p><strong>Nama Siswa:</strong> {{ $record->nama_siswa }}</p>
                <p><strong>Kelas:</strong> {{ $record->kelas }}</p>
                <p><strong>Nama Barang:</strong> {{ $record->nama_barang }}</p>
                <p><strong>Jumlah:</strong> {{ $record->quantity }}</p>
                <p><strong>Guru Mapel:</strong> {{ $record->guru_mapel }}</p>
                <p><strong>Ruangan:</strong> {{ $record->ruangan }}</p>
                <p><strong>Deskripsi:</strong> {{ $record->description }}</p>
                <p><strong>Status Peminjaman :</strong>{{ $record->status }}</p>
                <p><strong>Tanggal Peminjaman:</strong> {{ $record->created_at->format('Y-m-d H:i') }}</p>
                <p><strong>Tanggal Pengembalian :</strong>{{ $record->updated_at->format('Y-m-d H:i')}}</p>
            </div>
        </div>
    </div>

 
</body>
</html>
