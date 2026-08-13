<x-app-layout>
    <x-slot name="header">Ubah Laporan Kegiatan</x-slot>
    <x-kegiatan-submenu />

    <div class="card border-0 shadow-sm p-4 mb-5">
        <h5 class="fw-bold mb-3">{{ $laporanKegiatan->kegiatan->nama_kegiatan }}</h5>
        <form method="POST" action="{{ route('admin.laporan-kegiatan.update', $laporanKegiatan) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.kegiatan.laporan._form')
        </form>
    </div>
</x-app-layout>
