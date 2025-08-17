@extends("template_admin.layout")

@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <nav class="d-flex align-items-center mb-2">
                            <a
                                href="{{ route("superadmin.dashboard") }}"
                                class="text-muted me-2"
                            >
                                <i class="bx bx-home-alt me-1"></i>
                                Dashboard
                            </a>
                            <i class="bx bx-chevron-right me-2"></i>
                            <span class="text-dark">Kelola Template Bahan</span>
                        </nav>
                        <h4 class="mb-1">Kelola Template Bahan</h4>
                        <p class="mb-0 text-muted">
                            Daftar template bahan untuk menu makanan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <form
                            action="{{ route("superadmin.template-items.index") }}"
                            method="GET"
                            class="d-flex"
                        >
                            <input
                                type="text"
                                name="search"
                                class="form-control me-2"
                                placeholder="Cari nama bahan..."
                                value="{{ request("search") }}"
                            />
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i>
                                Cari
                            </button>
                        </form>
                    </div>
                    {{-- @hasrole('super_admin|ahli_gizi') --}}
                    <div class="col-md-6 text-md-end">
                        <a
                            href="{{ route("superadmin.template-items.create") }}"
                            class="btn btn-primary"
                        >
                            <i class="bx bx-plus"></i>
                            Tambah Template Bahan
                        </a>
                    </div>
                    {{-- @endhasrole --}}
                </div>
            </div>
        </div>

        <!-- Template Items Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Bahan</th>
                                <th>Satuan</th>
                                <th>Keterangan</th>
                                {{-- @hasrole('super_admin|ahli_gizi') --}}
                                <th>Aksi</th>
                                {{-- @endhasrole --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templateItems as $index => $item)
                                <tr>
                                    <td>
                                        {{ $templateItems->firstItem() + $index }}
                                    </td>
                                    <td>{{ $item->nama_bahan }}</td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>{{ $item->keterangan ?: "-" }}</td>
                                    {{-- @hasrole('super_admin|ahli_gizi') --}}
                                    <td>
                                        <div class="dropdown">
                                            <button
                                                type="button"
                                                class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"
                                            >
                                                <i
                                                    class="bx bx-dots-vertical-rounded"
                                                ></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route("superadmin.template-items.edit", $item) }}"
                                                >
                                                    <i
                                                        class="bx bx-edit-alt me-1"
                                                    ></i>
                                                    Edit
                                                </a>
                                                <form
                                                    action="{{ route("superadmin.template-items.destroy", $item) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus template bahan ini?');"
                                                >
                                                    @csrf
                                                    @method("DELETE")
                                                    <button
                                                        type="submit"
                                                        class="dropdown-item"
                                                    >
                                                        <i
                                                            class="bx bx-trash me-1"
                                                        ></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- @endhasrole --}}
                                </tr>
                            @empty
                                <tr>
                                    {{-- <td colspan="{{ auth()->user()->hasRole('super_admin|ahli_gizi') ? 5 : 4 }}" class="text-center">Tidak ada data template bahan.</td> --}}
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $templateItems->appends(["search" => request("search")])->links() }}
                </div>
            </div>
        </div>

        <!-- Instructions Alert -->
        <div class="alert alert-info alert-dismissible mt-4" role="alert">
            <h6 class="alert-heading mb-2">Instruksi Kelola Template Bahan</h6>
            <ul class="mb-0">
                <li>
                    Gunakan pencarian untuk menemukan bahan berdasarkan nama.
                </li>
                <li>
                    Hanya Super Admin dan Ahli Gizi yang dapat menambah,
                    mengedit, atau menghapus template bahan.
                </li>
                <li>
                    Template bahan yang digunakan dalam menu atau stock tidak
                    dapat dihapus.
                </li>
            </ul>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    </div>
@endsection
