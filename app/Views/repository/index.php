<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Repository Digital</h4>

<form method="get" class="row g-2 mb-4">
    <div class="col-md-3">
        <input type="text" name="keyword" class="form-control" placeholder="Kata kunci / abstrak..."
               value="<?= esc($filters['keyword'] ?? '') ?>" id="search-keyword" autocomplete="off">
        <div id="suggest-box" class="list-group position-absolute" style="z-index:1000;"></div>
    </div>
    <div class="col-md-2"><input type="text" name="judul" class="form-control" placeholder="Judul" value="<?= esc($filters['judul'] ?? '') ?>"></div>
    <div class="col-md-2"><input type="text" name="penulis" class="form-control" placeholder="Penulis" value="<?= esc($filters['penulis'] ?? '') ?>"></div>
    <div class="col-md-1"><input type="number" name="tahun" class="form-control" placeholder="Tahun" value="<?= esc($filters['tahun'] ?? '') ?>"></div>
    <div class="col-md-2">
        <select name="kategori_id" class="form-select">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategori as $k): ?>
                <option value="<?= $k['id'] ?>" <?= ($filters['kategori_id'] ?? '') == $k['id'] ? 'selected' : '' ?>><?= esc($k['nama']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Cari</button></div>
</form>

<div class="row">
    <?php foreach ($dokumen as $d): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <?php if (!empty($d['cover_thumbnail'])): ?>
                    <img src="<?= base_url('cover-image/' . $d['cover_thumbnail']) ?>" class="card-img-top" style="height:160px; object-fit:cover;">
                <?php endif; ?>
                <div class="card-body">
                    <span class="badge bg-secondary mb-2"><?= esc($d['kategori_nama']) ?></span>
                    <h6 class="card-title"><?= esc($d['judul']) ?></h6>
                    <p class="card-text small text-muted mb-1"><?= esc($d['penulis']) ?> · <?= esc($d['tahun']) ?></p>
                    <a href="<?= base_url('dokumen/' . $d['id']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $pager->links('dokumen', 'default_full') ?>

<?= $this->section('scripts') ?>
<script>
const input = document.getElementById('search-keyword');
const box = document.getElementById('suggest-box');
let timer;
input.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(async () => {
        if (input.value.length < 2) { box.innerHTML = ''; return; }
        const res = await fetch(`<?= base_url('dokumen/suggest') ?>?q=${encodeURIComponent(input.value)}`);
        const data = await res.json();
        box.innerHTML = data.map(d => `<a href="<?= base_url('dokumen') ?>/${d.id}" class="list-group-item list-group-item-action">${d.judul}</a>`).join('');
    }, 300);
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
