<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventory</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Inventory Produk</h2>

    <div class="card mb-4">
        <div class="card-body">
            <div class="form-row">
                <div class="col">
                    <input id="name" class="form-control" placeholder="Nama Produk">
                </div>
                <div class="col">
                    <input id="price" type="number" class="form-control" placeholder="Harga">
                </div>
                <div class="col">
                    <input id="stock" type="number" class="form-control" placeholder="Stok">
                </div>
                <div class="col">
                    <button class="btn btn-primary btn-block" onclick="add()">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
        <tr>
            <th>Nama</th>
            <th>Harga</th>
            <th>Stok</th>
            <th width="120">Aksi</th>
        </tr>
        </thead>
        <tbody id="tbl"></tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const tbl = document.getElementById('tbl');
    const nameInput  = document.getElementById('name');
    const priceInput = document.getElementById('price');
    const stockInput = document.getElementById('stock');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    /* ===== LOAD DATA ===== */
    async function load() {
        try {
            const r = await fetch('/api/products');
            const d = await r.json();

            const products = d.filter(p => Number(p.stock || 0) > 0);

            if (products.length === 0) {
                tbl.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Tidak ada produk tersedia
                        </td>
                    </tr>
                `;
                return;
            }

            tbl.innerHTML = products.map(p => `
                <tr>
                    <td>${p.name}</td>
                    <td>Rp ${Number(p.price).toLocaleString()}</td>
                    <td>${p.stock}</td>
                    <td>
                        <button class="btn btn-sm btn-danger"
                                onclick="sell(${p.id}, ${p.stock})">
                            Jual
                        </button>
                    </td>
                </tr>
            `).join('');

        } catch (e) {
            console.error(e);
            Swal.fire('Error', 'Gagal memuat data', 'error');
        }
    }

    /* ===== ADD PRODUCT ===== */
    window.add = async function () {
        if (!nameInput.value || !priceInput.value || !stockInput.value) {
            Swal.fire('Error', 'Semua field wajib diisi', 'warning');
            return;
        }

        const r = await fetch('/api/products', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                name: nameInput.value,
                price: priceInput.value,
                stock: stockInput.value
            })
        });

        if (!r.ok) {
            Swal.fire('Gagal', 'Produk gagal disimpan', 'error');
            return;
        }

        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Produk berhasil ditambahkan',
            timer: 1500,
            showConfirmButton: false
        });

        nameInput.value = '';
        priceInput.value = '';
        stockInput.value = '';

        load();
    };

    /* ===== SELL PRODUCT (MODAL) ===== */
    window.sell = async function (id, currentStock) {

        const { value: qty } = await Swal.fire({
            title: 'Jual Produk',
            input: 'number',
            inputLabel: `Stok tersedia: ${currentStock}`,
            inputAttributes: {
                min: 1
            },
            showCancelButton: true,
            confirmButtonText: 'Jual'
        });

        if (!qty) return;

        if (qty > currentStock) {
            Swal.fire('Error', 'Stok tidak cukup', 'error');
            return;
        }

        const r = await fetch(`/api/products/${id}/sell`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ qty })
        });

        if (!r.ok) {
            Swal.fire('Gagal', 'Stok tidak cukup', 'error');
            return;
        }

        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Transaksi berhasil',
            timer: 1200,
            showConfirmButton: false
        });

        load();
    };

    load();
});
</script>




</body>
</html>
