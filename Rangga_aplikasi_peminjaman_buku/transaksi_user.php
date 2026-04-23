<?php
include "cek_login.php";
include "koneksi.php";

$denda_per_hari = 1000;
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Transaksi</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200 p-6">

<div class="bg-white shadow rounded border">

    <!-- HEADER -->
    <div class="bg-blue-600 text-white px-4 py-2 rounded-t">
        Data Transaksi
    </div>

    <div class="p-4">

        <!-- TOP BAR -->
        <div class="flex justify-between mb-3">
            <div>
                <select class="border p-1">
                    <option>10</option>
                    <option>25</option>
                </select>
                records per page
            </div>

            <div>
                Search: <input type="text" class="border p-1">
            </div>
        </div>

        <!-- TABLE -->
        <table class="w-full border text-sm text-center">
            <tr class="bg-gray-100">
                <th class="border p-2">No</th>
                <th class="border p-2">Judul</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Tanggal Pinjam</th>
                <th class="border p-2">Tanggal Kembali</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Terlambat</th>
                <th class="border p-2">Aksi</th>
            </tr>

            <?php
            $no=1;

            $data = mysqli_query($conn,"SELECT transaksi.*, buku.judul, users.username 
            FROM transaksi
            JOIN buku ON transaksi.buku_id = buku.id
            JOIN users ON transaksi.user_id = users.id");

            while($d = mysqli_fetch_array($data)){

                $hari_ini = date('Y-m-d');
                $terlambat = 0;
                $denda = 0;

                if($hari_ini > $d['tanggal_jatuh_tempo'] && $d['status']=='dipinjam'){
                    $selisih = (strtotime($hari_ini) - strtotime($d['tanggal_jatuh_tempo'])) / 86400;
                    $terlambat = $selisih;
                    $denda = $terlambat * $denda_per_hari;
                }
            ?>

            <tr>
                <td class="border p-2"><?= $no++ ?></td>
                <td class="border p-2"><?= $d['judul'] ?></td>
                <td class="border p-2"><?= $d['username'] ?></td>
                <td class="border p-2"><?= $d['tanggal_pinjam'] ?></td>
                <td class="border p-2">
                    <?= $d['tanggal_kembali'] ?? '-' ?>
                </td>
                <td class="border p-2"><?= ucfirst($d['status']) ?></td>

                <td class="border p-2">
                    <?php
                    if($terlambat > 0){
                        echo "<span class='text-red-600 font-bold'>
                        $terlambat hari <br>(Rp $denda)
                        </span>";
                    } else {
                        echo "0 hari";
                    }
                    ?>
                </td>

                <td class="border p-2">
                    <?php if($d['status']=='dipinjam'){ ?>
                        <a href="kembali.php?id=<?= $d['id'] ?>"
                           class="bg-blue-500 text-white px-2 py-1 rounded">
                           Kembali
                        </a>

                        <a href="perpanjang.php?id=<?= $d['id'] ?>"
                           class="bg-red-500 text-white px-2 py-1 rounded">
                           Perpanjang
                        </a>
                    <?php } ?>
                </td>
            </tr>

            <?php } ?>

        </table>

        <!-- FOOTER TABLE -->
        <div class="flex justify-between mt-3">
            <div>Showing 1 to 3 of 3 entries</div>
            <div>
                <button class="border px-2 py-1">Previous</button>
                <button class="bg-blue-500 text-white px-2 py-1">1</button>
                <button class="border px-2 py-1">Next</button>
            </div>
        </div>

        <!-- BUTTON BAWAH -->
        <div class="mt-4 flex gap-2">
            <a href="tambah_transaksi.php" 
                 class="bg-green-500 text-white px-3 py-2 rounded">
                 + Tambah Data
            </a>

            <button class="bg-gray-300 px-3 py-2 rounded">
                ExportToExcel
            </button>

            <button class="bg-gray-300 px-3 py-2 rounded">
                ExportToPdf
            </button>
        </div>

    </div>
</div>

</body>
</html>