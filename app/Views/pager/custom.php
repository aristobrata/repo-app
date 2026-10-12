<?php
/**
 * Custom Bootstrap 5 pagination view for CodeIgniter 4.
 *
 * Cara pakai di view mana pun yang menampilkan pagination, ganti baris:
 *     <?= $pager->links() ?>
 * menjadi:
 *     <?= $pager->links('default', 'pager/custom') ?>
 *
 * Jika kamu memberi nama group pager custom saat memanggil paginate(),
 * misalnya $model->paginate(10, 'dokumen'), maka ganti 'default' di atas
 * menjadi 'dokumen' (harus sama dengan nama group yang dipakai).
 *
 * File ini WAJIB diletakkan tepat di:
 *     app/Views/pager/custom.php
 */

// Batasi hanya 3 kotak nomor halaman: 1 di kiri dan 1 di kanan dari halaman
// aktif. Tanpa baris ini, links() akan menampilkan SEMUA nomor halaman
// (bisa ratusan kotak), yang membuat baris pagination melebar dan tombol
// Next/Last jadi sulit/tidak bisa diklik karena tertimpa elemen lain.
$pager->setSurroundCount(1);
?>
<?php if ($pager->getPageCount() > 1): ?>
<nav aria-label="Pagination">
  <ul class="pagination justify-content-center mb-0">

    <?php if ($pager->hasPrevious()): ?>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getFirst() ?>">&laquo; First</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getPrevious() ?>">Prev</a>
      </li>
    <?php else: ?>
      <li class="page-item disabled">
        <span class="page-link">&laquo; First</span>
      </li>
      <li class="page-item disabled">
        <span class="page-link">Prev</span>
      </li>
    <?php endif; ?>

    <?php foreach ($pager->links() as $link): ?>
      <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
        <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
      </li>
    <?php endforeach; ?>

    <?php if ($pager->hasNext()): ?>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getNext() ?>">Next</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getLast() ?>">Last &raquo;</a>
      </li>
    <?php else: ?>
      <li class="page-item disabled">
        <span class="page-link">Next</span>
      </li>
      <li class="page-item disabled">
        <span class="page-link">Last &raquo;</span>
      </li>
    <?php endif; ?>

  </ul>
</nav>
<?php endif; ?>
