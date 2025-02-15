<div class="row">
	<div class="col-md-12">
	    <div class="panel panel-info">
	        <div class="panel-heading"><h3 class="text-center">LAPORAN NILAI PER MAHASISWA</h3></div>
	        <div class="panel-body">
			<form class="form-inline" action="<?= $_SERVER["REQUEST_URI"] ?>" method="post">
    		<label for="mhs">Mahasiswa :</label>
   				<select class="form-control" name="mhs">
        			<option> --- </option>
        			<?php 
        				$selected_npm = isset($_POST['mhs']) ? $_POST['mhs'] : '';
        				$q = $connection->query("SELECT * FROM mahasiswa WHERE npm IN (SELECT npm FROM hasil)"); 
        				if ($q->num_rows > 0):
            				while ($r = $q->fetch_assoc()): 
                				$selected = ($r['npm'] == $selected_npm) ? 'selected' : '';
                		?>
                				<option value="<?= $r['npm'] ?>" <?= $selected; ?>><?= $r['npm'] ?> | <?= $r['nama'] ?></option>
            				<?php endwhile; ?>
       					<?php else: ?>
            				<option value="">Belum ada data</option>
       					<?php endif; ?>
    			</select>
			<button type="submit" class="btn btn-primary">Tampilkan</button>
			</form>
			<?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <?php
    if ($_POST['mhs'] == '---') {
        // Menampilkan data kosong jika pilihan adalah ---
        $beasiswa = [];
        $data = [];
        $max = 0;
        $mhs_nama = $mhs_npm = "";
    } else {
        // Query normal jika mahasiswa dipilih
        $q = $connection->query("SELECT b.kd_beasiswa, b.nama, h.nilai, (SELECT MAX(nilai) FROM hasil WHERE npm=h.npm) AS nilai_max, m.nama AS mhs_nama, m.npm AS mhs_npm FROM mahasiswa m JOIN hasil h ON m.npm=h.npm JOIN beasiswa b ON b.kd_beasiswa=h.kd_beasiswa WHERE m.npm=$_POST[mhs]");
        $beasiswa = [];
        $data = [];
        $mhs_nama = $mhs_npm = "";
        while ($r = $q->fetch_assoc()) {
            $beasiswa[$r["kd_beasiswa"]] = $r["nama"];
            $data[$r["kd_beasiswa"]][] = $r["nilai"];
            $max = $r["nilai_max"];
            $mhs_nama = $r["mhs_nama"];
            $mhs_npm = $r["mhs_npm"];
        }
    }
    ?>
    <hr>
    <?php if ($_POST['mhs'] != '---'): ?>
        <div class="alert alert-info">
            <strong>Nama Mahasiswa:</strong> <?=$mhs_nama?> <br>
            <strong>NPM:</strong> <?=$mhs_npm?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <strong>Pilih mahasiswa untuk menampilkan data.</strong>
        </div>
    <?php endif; ?>
    <table class="table table-condensed">
        <tbody>
            <?php if ($_POST['mhs'] != '---'): ?>
                <?php $query = $connection->query("SELECT DISTINCT(p.kd_beasiswa), k.nama, n.nilai FROM nilai n JOIN penilaian p USING(kd_kriteria) JOIN kriteria k USING(kd_kriteria) WHERE n.npm=$_POST[mhs] AND n.kd_beasiswa=1"); ?>
                <?php while ($r = $query->fetch_assoc()): ?>
                    <tr>
                        <th><?=$r["nama"]?></th>
                        <td>: <?=number_format($r["nilai"], 8)?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" class="text-center">Tidak ada data yang ditampilkan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <hr>
    <table class="table table-condensed">
        <thead>
            <tr>
                <?php foreach ($beasiswa as $key => $val): ?>
                    <th><?=$val?></th>
                <?php endforeach; ?>
                <th>Nilai Maksimal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php foreach($beasiswa as $key => $val): ?>
                    <?php foreach($data[$key] as $v): ?>
                        <td><?=number_format($v, 8)?></td>
                    <?php endforeach ?>
                <?php endforeach ?>
                <td><?=number_format($max, 8)?></td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

	        </div>
	    </div>
	</div>
</div>
