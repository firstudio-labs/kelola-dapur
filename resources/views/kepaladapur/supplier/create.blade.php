@extends("template_kepala_dapur.layout")
@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kepala-dapur.supplier.index', ['dapur' => $dapur->id_dapur]) }}">Data Supplier</a></li>
                <li class="breadcrumb-item active">Tambah Supplier</li>
            </ol>
        </nav>

        <div class="card mb-4">
            <h5 class="card-header">Tambah Data Supplier</h5>
            <div class="card-body">
                <form action="{{ route('kepala-dapur.supplier.store', ['dapur' => $dapur->id_dapur]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="nama_supplier">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_supplier') is-invalid @enderror" id="nama_supplier" name="nama_supplier" value="{{ old('nama_supplier') }}" required />
                            @error('nama_supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="kontak">Kontak (No Tlp/WA)</label>
                            <input type="text" class="form-control @error('kontak') is-invalid @enderror" id="kontak" name="kontak" value="{{ old('kontak') }}" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="alamat">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="keterangan">Keterangan / Catatan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Simpan Supplier</button>
                        <a href="{{ route('kepala-dapur.supplier.index', ['dapur' => $dapur->id_dapur]) }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
