@extends("template_kepala_dapur.layout")
@section("content")
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route("dashboard") }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Kelola User</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Kelola User</h4>
                <p class="text-muted mb-0">
                    Daftar user admin gudang dan ahli gizi di dapur ini
                    @if ($current_user)
                        <br />
                        <small>
                            Dikelola oleh:
                            <strong>
                                {{ $current_user->nama ?? "Unknown" }}
                            </strong>
                            (Kepala Dapur)
                        </small>
                    @endif
                </p>
            </div>
            <a
                href="{{ route("kepala-dapur.users.create", ["dapur" => $dapur->id_dapur]) }}"
                class="btn btn-primary"
            >
                <i class="bx bx-plus me-1"></i>
                Tambah User
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session("success"))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session("success") }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif

        @if (session("error"))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session("error") }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif

        <!-- Search -->
        <div class="card mb-4">
            <div class="card-body py-3">
                <form
                    action="{{ route("kepala-dapur.users.index", ["dapur" => $dapur->id_dapur]) }}"
                    method="GET"
                    class="row align-items-center"
                >
                    <div class="col-md-10">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Cari nama, username, atau email..."
                            value="{{ request("search") }}"
                        />
                    </div>
                    <div class="col-md-2">
                        <button
                            type="submit"
                            class="btn btn-outline-primary w-100"
                        >
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div
                class="card-header d-flex justify-content-between align-items-center"
            >
                <h6 class="mb-0">Daftar User</h6>
                <small class="text-muted">
                    @if (request("search"))
                        Hasil pencarian:
                        <strong>{{ $users->total() }}</strong>
                        dari "{{ request("search") }}"
                    @else
                        Total:
                        <strong>{{ $users->total() }}</strong>
                        user
                    @endif
                </small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="25%">Nama</th>
                                <th width="20%">Username</th>
                                <th width="20%">Role</th>
                                <th width="10%" class="text-center">Status</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $index => $user)
                                <tr>
                                    <td class="text-center">
                                        {{ $users->firstItem() + $index }}
                                    </td>
                                    <td class="fw-semibold">
                                        {{ $user->nama }}
                                    </td>
                                    <td>{{ $user->username }}</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ ucfirst(str_replace("_", " ", $user->userRole->role_type)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-{{ $user->is_active ? "success" : "danger" }}"
                                        >
                                            {{ $user->is_active ? "Aktif" : "Non-Aktif" }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a
                                                href="{{ route("kepala-dapur.users.show", ["dapur" => $dapur->id_dapur, "user" => $user->id_user]) }}"
                                                class="btn btn-sm btn-outline-info"
                                                title="Lihat Detail"
                                            >
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a
                                                href="{{ route("kepala-dapur.users.edit", ["dapur" => $dapur->id_dapur, "user" => $user->id_user]) }}"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Edit"
                                            >
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <form
                                                action="{{ route("kepala-dapur.users.destroy", ["dapur" => $dapur->id_dapur, "user" => $user->id_user]) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus user {{ $user->nama }}?');"
                                            >
                                                @csrf
                                                @method("DELETE")
                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus"
                                                >
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            @if (request("search"))
                                                <p>
                                                    Tidak ditemukan hasil untuk
                                                    "
                                                    <strong>
                                                        {{ request("search") }}
                                                    </strong>
                                                    "
                                                </p>
                                                <a
                                                    href="{{ route("kepala-dapur.users.index", ["dapur" => $dapur->id_dapur]) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    Tampilkan Semua
                                                </a>
                                            @else
                                                <p>Belum ada user</p>
                                                <a
                                                    href="{{ route("kepala-dapur.users.create", ["dapur" => $dapur->id_dapur]) }}"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    Tambah User
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($users->hasPages())
                    <div class="mt-3">
                        {{ $users->appends(["search" => request("search")])->links("vendor.pagination.sneat") }}
                    </div>
                @endif
            </div>

            <!-- Table Info Footer -->
            @if ($users->count() > 0)
                <div class="card-footer">
                    <small class="text-muted">
                        Menampilkan {{ $users->firstItem() }} -
                        {{ $users->lastItem() }} dari {{ $users->total() }}
                        data
                        @if (request("search"))
                                untuk pencarian "{{ request("search") }}"
                        @endif
                    </small>
                </div>
            @endif
        </div>
    </div>

    @push("scripts")
        <script>
            // Auto hide alerts
            setTimeout(function () {
                $('.alert').fadeOut('slow');
            }, 4000);
        </script>
    @endpush
@endsection
