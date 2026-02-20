@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header dengan Tombol Kembali -->
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Form Pengajuan Surat</h1>
        <a href="{{ route('student.dashboard') }}" 
           class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md">
            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Warning Box (Peringatan Disesuaikan) -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-md shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm text-yellow-800 font-bold">⚠️ Perhatian Sebelum Mengajukan!</h3>
                <p class="text-sm text-yellow-700 mt-1 leading-relaxed">
                    Pastikan semua data yang Anda isi sudah benar dan teliti. 
                    <strong class="font-bold block mt-1">Sistem tidak memiliki fitur pembatalan mandiri oleh mahasiswa.</strong> 
                    Jika terdapat kesalahan setelah pengajuan, silakan menghubungi Admin TU secara langsung untuk proses pembatalan atau perbaikan.
                </p>
            </div>
        </div>
    </div>

    <!-- Form Utama -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden max-w-3xl border border-gray-100">
        <!-- Informasi Pemohon (Snapshot Preview) -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-800">Informasi Pemohon</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Profil Aktif</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-white p-3 rounded-md border border-gray-100">
                    <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Nama Lengkap</span>
                    <span class="font-semibold text-gray-800 text-base">{{ Auth::user()->name }}</span>
                </div>
                <div class="bg-white p-3 rounded-md border border-gray-100">
                    <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">NIM</span>
                    <span class="font-semibold text-gray-800 text-base">{{ Auth::user()->nim }}</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-blue-600 bg-blue-50 p-2 rounded-md">
                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Informasi:</strong> Data di atas akan otomatis disimpan (snapshot) ke dalam surat saat Anda mengajukan.</span>
            </div>
        </div>

        <div class="p-6">
            <!-- Error Validation -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-red-800">Terdapat kesalahan input:</h4>
                            <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('student.letters.store') }}" method="POST" class="space-y-6" x-data="{ jenisSurat: '{{ old('letter_type_id', '') }}' }">
                @csrf

                <!-- Select Jenis Surat -->
                <div>
                    <label for="letter_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Jenis Surat <span class="text-red-500">*</span>
                    </label>
                    <select name="letter_type_id" id="letter_type_id" required 
                            x-model="jenisSurat"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('letter_type_id') border-red-500 @enderror">
                        <option value="" disabled selected>-- Pilih Jenis Surat --</option>
                        @foreach($letterTypes as $type)
                            <option value="{{ $type->id }}" {{ old('letter_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->code }} - {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('letter_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dynamic Form: Kerja Praktek (ID = 2) -->
                <!-- CATATAN: Sesuaikan ID '2' dengan ID Kerja Praktek di database Anda -->
                <div x-show="jenisSurat == '2'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 shadow-sm">
                    
                    <div class="flex items-center mb-4 pb-3 border-b border-blue-200">
                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <h4 class="text-lg font-bold text-blue-900">Form Khusus: Surat Kerja Praktek</h4>
                    </div>

                    <div class="space-y-4">
                        <!-- Nama Instansi -->
                        <div>
                            <label for="nama_instansi" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Instansi Tujuan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_instansi" 
                                   id="nama_instansi" 
                                   value="{{ old('nama_instansi') }}"
                                   placeholder="Contoh: PT. Teknologi Indonesia Tbk"
                                   :required="jenisSurat == '2'"
                                   class="w-full px-4 py-2.5 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all @error('nama_instansi') border-red-500 @enderror">
                            @error('nama_instansi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alamat Instansi -->
                        <div>
                            <label for="alamat_instansi" class="block text-sm font-semibold text-gray-700 mb-2">
                                Alamat Instansi <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alamat_instansi" 
                                      id="alamat_instansi" 
                                      rows="3"
                                      placeholder="Contoh: Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10110"
                                      :required="jenisSurat == '2'"
                                      class="w-full px-4 py-2.5 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all @error('alamat_instansi') border-red-500 @enderror">{{ old('alamat_instansi') }}</textarea>
                            @error('alamat_instansi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Mulai KP -->
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Mulai Kerja Praktek <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="tanggal_mulai" 
                                   id="tanggal_mulai" 
                                   value="{{ old('tanggal_mulai') }}"
                                   :required="jenisSurat == '2'"
                                   class="w-full px-4 py-2.5 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all @error('tanggal_mulai') border-red-500 @enderror">
                            @error('tanggal_mulai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-blue-700 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Tanggal ini akan tercantum dalam surat permohonan KP
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dynamic/Additional Data -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Data Tambahan
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="tujuan_surat" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Surat / Nama Instansi
                            </label>
                            <input type="text" name="tujuan_surat" id="tujuan_surat" value="{{ old('tujuan_surat') }}"
                                   placeholder="Contoh: Manajer HRD PT. XYZ Indonesia" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('tujuan_surat') border-red-500 @enderror">
                            @error('tujuan_surat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 italic">Kosongkan jika tidak diperlukan untuk jenis surat yang dipilih.</p>
                        </div>

                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                                Keterangan / Alasan Pengajuan
                            </label>
                            <textarea name="keterangan" id="keterangan" rows="4" 
                                      placeholder="Tuliskan alasan atau detail tambahan lainnya dengan jelas..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('student.dashboard') }}" 
                       class="inline-flex justify-center items-center px-6 py-2.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center items-center px-8 py-2.5 border border-transparent text-sm font-bold rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Informasi Tambahan -->
    <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-200 max-w-3xl">
        <div class="flex items-start">
            <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">📋 Alur Pengajuan Surat:</p>
                <ol class="list-decimal list-inside space-y-1 text-blue-700">
                    <li>Isi form dengan data yang lengkap dan benar</li>
                    <li>Admin akan memverifikasi pengajuan Anda (status: <span class="bg-yellow-100 px-2 py-0.5 rounded-full text-xs">Menunggu</span>)</li>
                    <li>Jika disetujui, surat akan diproses (status: <span class="bg-blue-100 px-2 py-0.5 rounded-full text-xs">Diproses</span>)</li>
                    <li>Surat selesai dan dapat diunduh (status: <span class="bg-green-100 px-2 py-0.5 rounded-full text-xs">Selesai</span>)</li>
                    <li class="text-red-600">Jika ada kesalahan, hubungi Admin TU untuk bantuan</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .transform {
        transition-property: transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    .hover\:-translate-y-0\.5:hover {
        transform: translateY(-2px);
    }
    
    .hover\:shadow-lg:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush