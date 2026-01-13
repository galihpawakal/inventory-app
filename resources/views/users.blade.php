<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
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
        <h2>Manajemen User</h2>
        <button class="btn btn-success btn-sm" @click="addUser">
            Tambah User
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">

            <!-- loading indicator -->
            <div class="p-4 text-center" x-show="loading">
                Loading...
            </div>

            <!-- table -->
            <template x-if="!loading">
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
                    <template x-for="u in users" :key="u.id + '-' + u.role_id">
                        <tr>
                            <td x-text="`${u.name} - ${roleName(u.role_id)}`"></td>
                            <td x-text="u.email"></td>
                            <td>
                            <select class="form-control form-control-sm"
                                    x-model.number="u.role_id">
                                <template x-for="r in roles" :key="r.id">
                                    <option
                                        :value="Number(r.id)"
                                        :selected="Number(r.id) === Number(u.role_id)"
                                        x-text="r.name">
                                    </option>
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
            </template>
        </div>
    </div>
</div>

<script>
function userStore() {
    return {
        users: [],
        roles: [],
        loading: true,
        csrf: document.querySelector('meta[name="csrf-token"]').content,

        /* ===== LOAD ALL (AMAN) ===== */
        async load() {
            try {
                this.loading = true;

                const rr = await fetch('/api/roles');
                this.roles = rr.ok ? await rr.json() : [];

                const ru = await fetch('/api/users');
                const users = await ru.json();

                this.users = users.map(u => ({
                    ...u,
                    role_id: Number(u.role_id)
                }));

            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        roleName(role_id) {
            const r = this.roles.find(r => Number(r.id) === Number(role_id));
            return r ? r.name : '-';
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
                timer: 900,
                showConfirmButton: false
            });
        },

        /* ===== ADD USER ===== */
        async addUser() {
            const roleOptions = this.roles
                .map(r => `<option value="${r.id}">${r.name}</option>`)
                .join('');

            const { value: form } = await Swal.fire({
                title: 'Tambah User',
                html: `
                    <input id="u-name" class="form-control mb-2" placeholder="Nama">
                    <input id="u-email" class="form-control mb-2" placeholder="Email">
                    <input id="u-password" type="password" class="form-control mb-2" placeholder="Password">
                    <select id="u-role" class="form-control">
                        ${roleOptions}
                    </select>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                preConfirm: () => {
                    const name = u('u-name').value.trim();
                    const email = u('u-email').value.trim();
                    const password = u('u-password').value;
                    const role_id = parseInt(u('u-role').value);

                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (!name || !email || !password) {
                        Swal.showValidationMessage('Semua field wajib diisi');
                        return;
                    }

                    if (!emailRegex.test(email)) {
                        Swal.showValidationMessage('Format email tidak valid');
                        return;
                    }

                    if (password.length < 8) {
                        Swal.showValidationMessage('Password minimal 8 karakter');
                        return;
                    }

                    return { name, email, password, role_id };
                }

            });

            if (!form) return;

            await fetch('/api/users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf
                },
                body: JSON.stringify(form)
            });

            await this.load();
        }
    }
    
}

function u(id) {
    return document.getElementById(id);
}
</script>

</body>
</html>
