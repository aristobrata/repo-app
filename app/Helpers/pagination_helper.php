<?php

/**
 * Helper "Tampilkan 25 / 50 / 100 data per halaman" untuk semua tabel berpaginasi.
 *
 * Pemakaian di controller :  ->paginate(per_page_from_request(), 'group')
 * Pemakaian di view       :  <?= per_page_bar('group', $pager) ?>
 * Untuk halaman dengan dua tabel (mis. Inovasi & KM) pakai $key berbeda,
 * contoh 'km_per_page', supaya pilihan masing-masing tabel tidak saling timpa.
 */

if (! function_exists('per_page_options')) {
    function per_page_options(): array
    {
        return [25, 50, 100];
    }
}

if (! function_exists('per_page_from_request')) {
    function per_page_from_request(string $key = 'per_page'): int
    {
        $nilai = (int) service('request')->getGet($key);
        return in_array($nilai, per_page_options(), true) ? $nilai : per_page_options()[0];
    }
}

if (! function_exists('per_page_bar')) {
    /**
     * Render baris "Tampilkan [25|50|100] data  ·  Menampilkan a–b dari N".
     * Mengganti pilihan akan memuat ulang halaman dengan kembali ke halaman 1
     * dan semua filter/pencarian yang sedang aktif tetap dipertahankan.
     */
    function per_page_bar(string $group, $pager, string $key = 'per_page'): string
    {
        $perPage = per_page_from_request($key);
        $total   = (int) $pager->getTotal($group);
        $page    = (int) $pager->getCurrentPage($group);
        $awal    = $total === 0 ? 0 : (($page - 1) * $perPage) + 1;
        $akhir   = min($total, $page * $perPage);
        $paramHal = $group === 'default' ? 'page' : 'page_' . $group;

        $opsi = '';
        foreach (per_page_options() as $o) {
            $opsi .= '<option value="' . $o . '"' . ($o === $perPage ? ' selected' : '') . '>' . $o . '</option>';
        }

        // PENTING: pakai window.URL, bukan URL -- di dalam atribut onchange, "URL" bisa terbaca
        // sebagai document.URL (string) sehingga muncul error "URL is not a constructor".
        $js = "var u=new window.URL(window.location.href);u.searchParams.set('" . esc($key, 'js') . "',this.value);"
            . "u.searchParams.delete('" . esc($paramHal, 'js') . "');window.location.href=u.toString();";

        return '<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 my-2 small">'
            . '<div class="d-flex align-items-center gap-2"><label class="mb-0 text-muted">Tampilkan</label>'
            . '<select class="form-select form-select-sm" style="width:auto" onchange="' . esc($js, 'attr') . '">' . $opsi . '</select>'
            . '<span class="text-muted">data per halaman</span></div>'
            . '<div class="text-muted">Menampilkan ' . number_format($awal) . '&ndash;' . number_format($akhir) . ' dari ' . number_format($total) . ' data</div>'
            . '</div>';
    }
}
