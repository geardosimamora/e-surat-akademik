<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full text-center border-t-4 {{ $letter->status === 'approved' ? 'border-green-500' : 'border-red-500' }}">
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Universitas Malikussaleh</h1>
        <p class="text-sm text-gray-500 mb-6">Sistem Verifikasi E-Surat Prodi Ilmu Politik</p>

        @if($letter->status === 'approved')
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
                <svg class="w-12 h-12 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-xl font-bold">DOKUMEN VALID</h2>
            </div>
            
            <div class="text-left text-gray-700 space-y-2 text-sm">
                <p><strong>Nomor Surat:</strong> {{ $letter->letter_number ?? '-' }}</p>
                <p><strong>Jenis Surat:</strong> {{ $letter->letterType->name }}</p>
                <p><strong>Nama Mahasiswa:</strong> {{ $letter->user_snapshot['name'] ?? 'Tidak diketahui' }}</p>
                <p><strong>NIM:</strong> {{ $letter->user_snapshot['nim'] ?? '-' }}</p>
                <p><strong>Tanggal Disetujui:</strong> {{ $letter->updated_at->format('d M Y') }}</p>
            </div>
        @else
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
                <svg class="w-12 h-12 mx-auto mb-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-xl font-bold">DOKUMEN TIDAK VALID / DIBATALKAN</h2>
            </div>
            
            <p class="text-gray-600 text-sm">
                Dokumen ini tidak terdaftar sebagai dokumen yang disetujui dalam sistem kami, atau telah ditarik kembali oleh pihak Program Studi.
            </p>
        @endif

        <div class="mt-8 pt-4 border-t border-gray-200 text-xs text-gray-400">
            ID Dokumen: {{ $letter->id }}
        </div>
    </div>

</body>
</html>