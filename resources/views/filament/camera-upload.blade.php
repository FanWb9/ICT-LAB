<div x-data="{ imageData: '', captureImage() { 
        const video = document.getElementById('video');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        // Konversi gambar ke format Base64
        this.imageData = canvas.toDataURL('image/png');
        
        // Mengirim gambar ke Livewire
        @this.set('{{ $getStatePath() }}', this.imageData); 
        
        // Sembunyikan video setelah capture
        video.style.display = 'none';
    } }" class="flex flex-col space-y-4">

    <div class="flex space-x-4 mb-4">
        <!-- Video Kamera -->
        <div class="flex-1">
            <video id="video" class="w-full h-auto border" autoplay></video>
        </div>
        {{-- data = camvas --}}
        <!-- Gambar hasil tangkapan -->
        <div class="flex-1">
            <img x-show="imageData" :src="imageData" class="w-full h-auto border border-gray-300 rounded-lg shadow-md" />
        </div>
    </div>

    <!-- Tombol Ambil Foto, hanya muncul jika belum ada gambar -->
    <button type="button" @click="captureImage()" x-show="!imageData" class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
        Ambil Foto
    </button>

    <!-- Input hidden untuk menyimpan data gambar dalam format Base64 -->
    <input type="hidden" id="image_data" name="{{ $getStatePath() }}" x-ref="image" :value="imageData" />
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const video = document.getElementById('video');

        // Mengakses kamera pengguna
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => video.srcObject = stream)
            .catch(error => console.error('Kamera tidak bisa diakses:', error));
    });
</script> 
