<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
requireRole('dosen');

// Dukungan FPDF manual maupun Composer.
$fpdf = __DIR__ . '/../lib/fpdf/fpdf.php';
if (is_file($fpdf)) {
    require_once $fpdf;
} elseif (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    http_response_code(500);
    exit('FPDF belum ditemukan. Letakkan folder FPDF di qr_absensi/lib/fpdf/ sehingga file qr_absensi/lib/fpdf/fpdf.php tersedia.');
}

if (!class_exists('FPDF')) {
    http_response_code(500);
    exit('Class FPDF tidak ditemukan. Pastikan file fpdf.php dan folder font FPDF ikut disalin.');
}

$didStmt = $pdo->prepare("SELECT d.id, d.nidn, d.nama FROM dosen d JOIN users u ON u.username = d.nidn WHERE u.id = ? LIMIT 1");
$didStmt->execute([userId()]);
$dosen = $didStmt->fetch(PDO::FETCH_ASSOC);
if (!$dosen) {
    http_response_code(404);
    exit('Data dosen tidak ditemukan.');
}
$did = (int)$dosen['id'];

$st = $pdo->prepare("SELECT
    s.id AS sesi_id,
    s.tanggal,
    s.pertemuan,
    s.jam_mulai,
    s.jam_selesai,
    mk.kode_mk,
    mk.nama_mk,
    k.nama_kelas,
    COUNT(DISTINCT km.mahasiswa_id) AS total,
    COALESCE(SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END), 0) AS hadir,
    COALESCE(SUM(CASE WHEN a.status = 'Terlambat' THEN 1 ELSE 0 END), 0) AS terlambat,
    COALESCE(SUM(CASE WHEN a.status IN ('Izin','Sakit') THEN 1 ELSE 0 END), 0) AS izin_sakit,
    COALESCE(SUM(CASE WHEN a.status = 'Tidak Hadir' OR a.id IS NULL THEN 1 ELSE 0 END), 0) AS tidak_hadir
FROM sesi_absensi s
JOIN jadwal j ON j.id = s.jadwal_id
JOIN mata_kuliah mk ON mk.id = j.mata_kuliah_id
JOIN kelas k ON k.id = j.kelas_id
JOIN kelas_mahasiswa km ON km.kelas_id = k.id
LEFT JOIN absensi a ON a.sesi_id = s.id AND a.mahasiswa_id = km.mahasiswa_id
WHERE j.dosen_id = ?
GROUP BY s.id, s.tanggal, s.pertemuan, s.jam_mulai, s.jam_selesai, mk.kode_mk, mk.nama_mk, k.nama_kelas
ORDER BY s.tanggal DESC, s.id DESC");
$st->execute([$did]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

function pdfText(string $text): string {
    return iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text);
}

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 12);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 9, pdfText('LAPORAN PRESENSI'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, pdfText('Sistem Absensi QR'), 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 6, pdfText('Dosen'), 0, 0);
$pdf->Cell(100, 6, ': '.pdfText((string)$dosen['nama']), 0, 1);
$pdf->Cell(30, 6, pdfText('NIDN'), 0, 0);
$pdf->Cell(100, 6, ': '.pdfText((string)$dosen['nidn']), 0, 1);
$pdf->Ln(4);

$headers = [
    ['Tanggal', 25],
    ['Pert.', 14],
    ['Mata Kuliah', 65],
    ['Kelas', 35],
    ['Total', 20],
    ['Hadir', 20],
    ['Terlambat', 23],
    ['Izin/Sakit', 25],
    ['Tidak Hadir', 30],
];

$pdf->SetFillColor(235, 235, 235);
$pdf->SetFont('Arial', 'B', 8);
foreach ($headers as [$label, $width]) {
    $pdf->Cell($width, 8, pdfText($label), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
foreach ($rows as $r) {
    $mk = ($r['kode_mk'] ? $r['kode_mk'].' - ' : '').$r['nama_mk'];
    $values = [
        [$r['tanggal'], 25, 'C'],
        [$r['pertemuan'], 14, 'C'],
        [$mk, 65, 'L'],
        [$r['nama_kelas'], 35, 'C'],
        [$r['total'], 20, 'C'],
        [$r['hadir'], 20, 'C'],
        [$r['terlambat'], 23, 'C'],
        [$r['izin_sakit'], 25, 'C'],
        [$r['tidak_hadir'], 30, 'C'],
    ];
    foreach ($values as [$value, $width, $align]) {
        $pdf->Cell($width, 8, pdfText((string)$value), 1, 0, $align);
    }
    $pdf->Ln();
}

if (!$rows) {
    $pdf->Cell(257, 10, pdfText('Belum ada data presensi.'), 1, 1, 'C');
}

$pdf->Ln(6);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, pdfText('Dicetak: '.date('d-m-Y H:i').' WIB'), 0, 1, 'R');

$safeName = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string)$dosen['nama']);
$pdf->Output('D', 'laporan-presensi-'.$safeName.'.pdf');
exit;
