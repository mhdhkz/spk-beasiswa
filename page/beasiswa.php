<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
	$sql = $connection->query("SELECT * FROM beasiswa WHERE kd_beasiswa='$_GET[key]'");
	$row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$validasi = false; $err = false;
	$nama = trim($_POST['nama']);
	$tahun = trim($_POST['tahun']);
	$tahun_sekarang = date("Y");
	$tahun_maksimal = $tahun_sekarang + 0;

	if (empty($nama)) {
		echo alert("Nama beasiswa tidak boleh kosong!", "?page=beasiswa");
		exit;
	}
	if (!is_numeric($tahun) || strlen($tahun) > 4 || $tahun <> $tahun_maksimal) {
		echo alert("Tahun harus berupa angka, maksimal 4 digit, dan tidak lebih dan kurang dari tahun saat ini!", "?page=beasiswa");
		exit;
	}
	if ($update) {
		$sql = "UPDATE beasiswa SET nama='$_POST[nama]', tahun='$_POST[tahun]' WHERE kd_beasiswa='$_GET[key]'";
	} else {
		$sql = "INSERT INTO beasiswa VALUES (NULL, '$_POST[nama]', '$_POST[tahun]')";
		$validasi = true;
	}

	if ($validasi) {
		$q = $connection->query("SELECT kd_beasiswa FROM beasiswa WHERE nama = '$_POST[nama]' AND tahun = '$_POST[tahun]'");
		if ($q->num_rows) {
			echo alert("Beasiswa sudah ada!", "?page=beasiswa");
			$err = true;
		}
	}

	if (!$err AND $connection->query($sql)) {
    	echo alert("Berhasil!", "?page=beasiswa");
  	} else {
		echo alert("Gagal!", "?page=beasiswa");
	}
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
  $connection->query("DELETE FROM beasiswa WHERE kd_beasiswa='$_GET[key]'");
	echo alert("Berhasil!", "?page=beasiswa");
}
?>
<div class="row">
	<div class="col-md-4">
	    <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
	        <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
	        <div class="panel-body">
	            <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
	                <div class="form-group">
	                    <label for="nama">Nama</label>
	                    <input type="text" name="nama" class="form-control" <?= (!$update) ?: 'value="'.$row["nama"].'"' ?>>
	                </div>
					<div class="form-group">
	                    <label for="nama">Tahun</label>
	                    <input type="number" name="tahun" class="form-control" <?= (!$update) ?: 'value="'.$row["tahun"].'"' ?>>
	                </div>
	                <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
	                <?php if ($update): ?>
						<a href="?page=beasiswa" class="btn btn-info btn-block">Batal</a>
					<?php endif; ?>
	            </form>
	        </div>
	    </div>
	</div>
	<div class="col-md-8">
	    <div class="panel panel-info">
	        <div class="panel-heading"><h3 class="text-center">DAFTAR BEASISWA</h3></div>
	        <div class="panel-body">
	            <table class="table table-condensed">
	                <thead>
	                    <tr>
	                        <th>No</th>
	                        <th>Nama</th>
							<th>Tahun</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT * FROM beasiswa")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
	                            <td><?=$row['nama']?></td>
								<td><?=$row['tahun']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=beasiswa&action=update&key=<?=$row['kd_beasiswa']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=beasiswa&action=delete&key=<?=$row['kd_beasiswa']?>" class="btn btn-danger btn-xs">Hapus</a>
	                                </div>
	                            </td>
	                        </tr>
	                        <?php endwhile ?>
	                    <?php endif ?>
	                </tbody>
	            </table>
	        </div>
	    </div>
	</div>
</div>
