<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
    $sql = $connection->query("SELECT * FROM nilai JOIN penilaian USING(kd_kriteria) WHERE kd_nilai='$_GET[key]'");
    $row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_POST["save"])) {
    $validasi = false; $err = false;
    $npm = $_POST['npm'];
    $q_npm = $connection->query("SELECT * FROM mahasiswa WHERE npm= '$npm'");
    $kd_beasiswa = $_POST['kd_beasiswa'];
    $q_beasiswa = $connection->query("SELECT * FROM beasiswa WHERE kd_beasiswa = '$kd_beasiswa'");

    if ($q_npm->num_rows == 0) {
        echo alert("Silahkan pilih mahasiswa terlebih dahulu!", "?page=nilai");
        exit;
    }
    if ($q_beasiswa->num_rows == 0) {
        echo alert("Beasiswa tidak ditemukan!", "?page=nilai");
        exit;
    }

    if ($update) {
        $connection->begin_transaction();
        foreach ($_POST["nilai"] as $kd_kriteria => $nilai) {
            $sql = "UPDATE nilai SET nilai = '$nilai' WHERE kd_nilai='$_GET[key]' AND kd_kriteria='$kd_kriteria'";
            if (!$connection->query($sql)) {
                $connection->rollback();
                echo "Error: " . $connection->error;
                exit;
            }
        }
        $connection->commit();
		echo alert("Data berhasil diupdate!", "?page=nilai");
    } else {
        $q_nilai = $connection->query("SELECT * FROM nilai WHERE npm = '$npm' AND kd_beasiswa = '$kd_beasiswa'");
        if ($q_nilai->num_rows > 0) {
            echo alert("Kombinasi NPM dan Beasiswa sudah ada!", "?page=nilai");
            exit;
        }

        $sql = "INSERT INTO nilai (kd_beasiswa, kd_kriteria, npm, nilai) VALUES ";
        foreach ($_POST["nilai"] as $kd_kriteria => $nilai) {
            $sql .= "('$kd_beasiswa', '$kd_kriteria', '$npm', '$nilai'),";
        }
        $sql = rtrim($sql, ',');

        // Pastikan insert hanya dieksekusi sekali
        if (!$connection->query($sql)) {
            echo "Error: " . $connection->error;
            exit;
        }

        $validasi = true;
    }

    if ($validasi) {
        echo alert("Berhasil!", "?page=nilai");
    } else {
        echo alert("Gagal!", "?page=nilai");
    }
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
    $connection->query("DELETE FROM nilai WHERE kd_nilai='$_GET[key]'");
    echo alert("Berhasil!", "?page=nilai");
}
?>

<div class="row">
	<div class="col-md-4">
	    <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
	        <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
	        <div class="panel-body">
	            <form action="<?=$_SERVER["REQUEST_URI"]?>" method="post">
					<div class="form-group">
						<label for="npm">Mahasiswa</label>
							<?php if ($_POST): 
							$npm = $_POST['npm'];
							$q_npm = $connection->query("SELECT * FROM mahasiswa WHERE npm= '$npm'");
							if ($q_npm->num_rows == 0) {
								echo alert("Silahkan pilih mahasiswa terlebih dahulu!", "?page=nilai");
								exit;
							}
							?>
								<input type="text" name="npm" value="<?=$_POST["npm"]?>" class="form-control" readonly="on">
							<?php else: ?>
								<select class="form-control" name="npm">
									<option>---</option>
										<?php $sql = $connection->query("SELECT * FROM mahasiswa"); while ($data = $sql->fetch_assoc()): ?>
											<option value="<?=$data["npm"]?>" <?= (!$update) ? "" : (($row["npm"] != $data["npm"]) ? "" : 'selected="selected"') ?>><?=$data["npm"]?> | <?=$data["nama"]?></option>
										<?php endwhile; ?>
								</select>
							<?php endif; ?>
					</div>
					<div class="form-group">
	                	<label for="kd_beasiswa">Beasiswa</label>
							<?php if ($_POST):
							$kd_beasiswa = $_POST['kd_beasiswa'];
							$q_beasiswa = $connection->query("SELECT * FROM beasiswa WHERE kd_beasiswa = '$kd_beasiswa'");
							if ($q_beasiswa->num_rows == 0) {
								echo alert("Silahkan pilih beasiswa terlebih dahulu!", "?page=nilai");
									exit;
								}
							?>
								<?php $q = $connection->query("SELECT nama FROM beasiswa WHERE kd_beasiswa='$_POST[kd_beasiswa]'"); ?>
									<input type="text"value="<?=$q->fetch_assoc()["nama"]?>" class="form-control" readonly="on">
									<input type="hidden" name="kd_beasiswa" value="<?=$_POST["kd_beasiswa"]?>">
							<?php else: ?>
								<select class="form-control" name="kd_beasiswa" id="beasiswa">
									<option>---</option>
										<?php $sql = $connection->query("SELECT * FROM beasiswa"); while ($data = $sql->fetch_assoc()): ?>
											<option value="<?=$data["kd_beasiswa"]?>"<?= (!$update) ? "" : (($row["kd_beasiswa"] != $data["kd_beasiswa"]) ? "" : 'selected="selected"') ?>><?=$data["nama"]?></option>
										<?php endwhile; ?>
								</select>
							<?php endif; ?>
					</div>
							<?php if ($_POST):?>
								<?php $q = $connection->query("SELECT * FROM kriteria WHERE kd_beasiswa=$_POST[kd_beasiswa]"); while ($r = $q->fetch_assoc()): ?>
				    	            <div class="form-group">
					                  <label for="nilai"><?=ucfirst($r["nama"])?></label>
											<select class="form-control" name="nilai[<?=$r["kd_kriteria"]?>]" id="nilai">
												<option>---</option>
												<?php $sql = $connection->query("SELECT * FROM penilaian WHERE kd_kriteria=$r[kd_kriteria]"); while ($data = $sql->fetch_assoc()): ?>
													<option value="<?=$data["bobot"]?>" class="<?=$data["kd_kriteria"]?>"<?= (!$update) ? "" : (($row["kd_penilaian"] != $data["kd_penilaian"]) ? "" : ' selected="selected"') ?>><?=$data["keterangan"]?></option>
												<?php endwhile; ?>
											</select>
				        	        </div>
								<?php endwhile; ?>
								<input type="hidden" name="save" value="true">
							<?php endif; ?>
	                <button type="submit" id="simpan" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block"><?=($_POST) ? "Simpan" : "Tampilkan"?></button>
	                <?php if ($update): ?>
						<a href="?page=nilai" class="btn btn-info btn-block">Batal</a>
					<?php endif; ?>
	            </form>
	        </div>
	    </div>
	</div>
	<div class="col-md-8">
	    <div class="panel panel-info">
	        <div class="panel-heading"><h3 class="text-center">DAFTAR PERSYARATAN</h3></div>
	        <div class="panel-body">
	            <table class="table table-condensed">
	                <thead>
	                    <tr>
	                        <th>No</th>
							<th>NPM</th>
							<th>Nama</th>
	                        <th>Beasiswa</th>
	                        <th>Kriteria</th>
	                        <th>Nilai</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT a.kd_nilai, c.nama AS nama_beasiswa, b.nama AS nama_kriteria, d.npm, d.nama AS nama_mahasiswa, a.nilai FROM nilai a JOIN kriteria b ON a.kd_kriteria=b.kd_kriteria JOIN beasiswa c ON a.kd_beasiswa=c.kd_beasiswa JOIN mahasiswa d ON d.npm=a.npm")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
								<td><?=$row['npm']?></td>
								<td><?=$row['nama_mahasiswa']?></td>
	                            <td><?=$row['nama_beasiswa']?></td>
	                            <td><?=$row['nama_kriteria']?></td>
	                            <td><?=$row['nilai']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=nilai&action=update&key=<?=$row['kd_nilai']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=nilai&action=delete&key=<?=$row['kd_nilai']?>" class="btn btn-danger btn-xs">Hapus</a>
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
<script type="text/javascript">
$("#kriteria").chained("#beasiswa");
$("#nilai").chained("#kriteria");
</script>
