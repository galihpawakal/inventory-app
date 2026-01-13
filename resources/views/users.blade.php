<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 4 -->
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light"
      x-data="userStore()"
      x-init="load()">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>👤 Manajemen User</h2>
        <button class="btn btn-success btn-sm" @click="addUser">
            ➕ Tambah User
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead class="thead-dark">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <template x-for="u in users" :key="u.id">
                    <tr>
                        <td x-text="u.name"></td>
                        <td x-text="u.email"></td>
                        <td>
                            <select class="form-control form-control-sm"
                                    x-model="u.role_id">
                                <template x-for="r in roles" :key="r.id">
                                    <option :value="r.id" x-text="r.name"></option>
                                </template>
                            </select>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary"
                                    @click="saveRole(u)">
                                Simpan
                            </button>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function userStore() {
    return {
        users: [],
        roles: [],
        csrf: document.querySelector('meta[name="csrf-token"]').content,

        /* ===== LOAD ALL ===== */
        async load() {
            await this.loadRoles();
            const r = await fetch('/api/users');
            this.users = await r.json();
        },

        async loadRoles() {
            const r = await fetch('/api/roles');
            this.roles = await r.json();
        },

        /* ===== UPDATE ROLE ===== */
        async saveRole(user) {
            const r = await fetch(`/api/users/${user.id}/change-role`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf
                },
                body: JSON.stringify({ role_id: user.role_id })
            });

            if (!r.ok) {
                Swal.fire('Gagal', 'Role tidak valid', 'error');
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Role user diperbarui',
                timer: 1200,
                showConfirmButton: false
            });
        },

        /* ===== ADD USER ===== */
        async addUser() {
            const roleOptions = this.roles.map(r =>
                `<option value="${r.id}">${r.name}</option>`
            ).join('');

            const { value: form } = await Swal.fire({
                title: 'Tambah User',
                html: `
                    <div class="text-left">
                        <div class="form-group">
                            <label>Nama</label>
                            <input id="u-name" class="form-control" placeholder="Nama">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input id="u-email" class="form-control" placeholder="Email">
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input id="u-password" type="password" class="form-control" placeholder="Password">
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select id="u-role" class="form-control">
                                ${roleOptions}
                            </select>
                        </div>
                    </div>

                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                focusConfirm: false,
                preConfirm: () => {
                    const name = document.getElementById('u-name').value;
                    const email = document.getElementById('u-email').value;
                    const password = document.getElementById('u-password').value;
                    const role_id = parseInt(document.getElementById('u-role').value);

                    if (!name || !email || !password) {
                        Swal.showValidationMessage('Semua field wajib diisi');
                        return;
                    }

                    // Validasi email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        Swal.showValidationMessage('Email tidak valid');
                        return;
                    }

                    // Validasi password minimal 8 karakter
                    if (password.length < 8) {
                        Swal.showValidationMessage('Password harus minimal 8 karakter');
                        return;
                    }

                    return { name, email, password, role_id };
                }

            });

            if (!form) return;

            const r = await fetch('/api/users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf
                },
                body: JSON.stringify(form)
            });

            if (!r.ok) {
                Swal.fire('Gagal', 'User gagal ditambahkan', 'error');
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'User berhasil ditambahkan',
                timer: 1200,
                showConfirmButton: false
            });

            this.load();
        }
    }
}
</script>

</body>
</html>
