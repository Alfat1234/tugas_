<?php
$file = "mahasiswa.json";

// Jika file belum ada → buat data awal
if (!file_exists($file)) {
    $data_awal = [
        ["nim"=>"2023001","nama"=>"Andi","prodi"=>"TI","alamat"=>"Bandung","gambar"=>""],
        ["nim"=>"2023002","nama"=>"Budi","prodi"=>"SI","alamat"=>"Jakarta","gambar"=>""],
        ["nim"=>"2023003","nama"=>"Citra","prodi"=>"TI","alamat"=>"Surabaya","gambar"=>""],
        ["nim"=>"2023004","nama"=>"Dewi","prodi"=>"SI","alamat"=>"Medan","gambar"=>""],
        ["nim"=>"2023005","nama"=>"Eka","prodi"=>"TI","alamat"=>"Bali","gambar"=>""],
        ["nim"=>"2023006","nama"=>"Fajar","prodi"=>"SI","alamat"=>"Lombok","gambar"=>""],
        ["nim"=>"2023007","nama"=>"Gita","prodi"=>"TI","alamat"=>"Semarang","gambar"=>""],
        ["nim"=>"2023008","nama"=>"Hadi","prodi"=>"SI","alamat"=>"Makassar","gambar"=>""],
        ["nim"=>"2023009","nama"=>"Indah","prodi"=>"TI","alamat"=>"Padang","gambar"=>""],
        ["nim"=>"2023010","nama"=>"Joko","prodi"=>"SI","alamat"=>"Palembang","gambar"=>""],
    ];
    file_put_contents($file, json_encode($data_awal));
}

// Ambil data
$data = json_decode(file_get_contents($file), true);

// ------------------ CREATE -------------------
if (isset($_POST["submit"])) {
    $imgBase64 = "";
    if (!empty($_FILES["gambar"]["tmp_name"])) {
        $imgData = file_get_contents($_FILES["gambar"]["tmp_name"]);
        $imgBase64 = "data:image/jpeg;base64,".base64_encode($imgData);
    }

    $baru = [
        "nim" => $_POST["nim"],
        "nama" => $_POST["nama"],
        "prodi" => $_POST["prodi"],
        "alamat" => $_POST["alamat"],
        "gambar" => $imgBase64
    ];

    $data[] = $baru;
    file_put_contents($file, json_encode($data));
    header("Location: index.php");
}

// ------------------ DELETE -------------------
if (isset($_GET["hapus"])) {
    $index = $_GET["hapus"];
    unset($data[$index]);
    $data = array_values($data);
    file_put_contents($file, json_encode($data));
    header("Location: index.php");
}

// ------------------ EDIT (TAMPILKAN FORM) -------------------
$editData = null;
if (isset($_GET["edit"])) {
    $editIndex = $_GET["edit"];
    $editData = $data[$editIndex];
}

// ------------------ UPDATE -------------------
if (isset($_POST["update"])) {
    $index = $_POST["index"];

    // Jika upload gambar baru → replace gambar
    if (!empty($_FILES['gambar']['tmp_name'])) {
        $imgData = file_get_contents($_FILES['gambar']['tmp_name']);
        $gambar = "data:image/jpeg;base64,".base64_encode($imgData);
    } else {
        // Jika tidak upload → pakai gambar lama
        $gambar = $data[$index]['gambar'];
    }

    $data[$index] = [
        "nim" => $_POST["nim"],
        "nama" => $_POST["nama"],
        "prodi" => $_POST["prodi"],
        "alamat" => $_POST["alamat"],
        "gambar" => $gambar
    ];

    file_put_contents($file, json_encode($data));
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html>
<body>

<h2><?= $editData ? "EDIT MAHASISWA" : "FORM TAMBAH MAHASISWA" ?></h2>

<form action="" method="POST" enctype="multipart/form-data">

	<input type="hidden" name="index" value="<?= $editData ? $editIndex : "" ?>">

	NIM <input type="text" name="nim" required value="<?= $editData["nim"] ?? "" ?>"><br>
	Nama <input type="text" name="nama" required value="<?= $editData["nama"] ?? "" ?>"><br>

	Prodi 
	<select name="prodi">
	    <option value="Teknik Informatika" <?= isset($editData) && $editData["prodi"]=="TI" ? "selected":"" ?>>Teknik Informatika</option>
	    <option value="Sistem Informasi" <?= isset($editData) && $editData["prodi"]=="SI" ? "selected":"" ?>>Sistem Informasi</option>
	</select><br>

	Alamat <textarea name="alamat"><?= $editData["alamat"] ?? "" ?></textarea><br>

	Gambar <input type="file" name="gambar"><br>

	<?php if ($editData && $editData["gambar"]): ?>
		<img src="<?= $editData["gambar"] ?>" width="80"><br>
	<?php endif; ?>

	<button type="submit" name="<?= $editData ? "update" : "submit" ?>">
	    <?= $editData ? "Update" : "Kirim" ?>
	</button>
</form>

<hr>

<h2>DATA MAHASISWA</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>No</th><th>NIM</th><th>Nama</th><th>Prodi</th><th>Alamat</th><th>Gambar</th><th>Aksi</th>
    </tr>

    <?php foreach($data as $i => $mhs): ?>
    <tr>
        <td><?= $i+1 ?></td>
        <td><?= $mhs["nim"] ?></td>
        <td><?= $mhs["nama"] ?></td>
        <td><?= $mhs["prodi"] ?></td>
        <td><?= $mhs["alamat"] ?></td>
        <td>
            <?php if ($mhs["gambar"]): ?>
                <img src="<?= $mhs['gambar'] ?>" width="80">
            <?php endif; ?>
        </td>
        <td>
            <a href="?edit=<?= $i ?>">Edit</a> |
            <a href="?hapus=<?= $i ?>" onclick="return confirm('Hapus?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
