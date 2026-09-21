<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/auth.php';
require_once __DIR__.'/../config/helpers.php';
requireRole('dosen');

/*
 * FPDF bisa dipasang dengan Composer:
 *   composer require setasign/fpdf:^1.9
 *
 * Atau letakkan library FPDF manual di:
 *   /lib/fpdf/fpdf.php
 */
$autoloadCandidates = [
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../vendor/autoload.php',
];

$autoloadLoaded = false;
foreach ($autoloadCandidates as $autoload) {
    if (is_file($autoload)) {
        require_once $autoload;
        $autoloadLoaded = true;
        break;
    }
}

if (!class_exists('FPDF')) {
    $manualFpdf = __DIR__.'/../lib/fpdf/fpdf.php';
    if (is_file($manualFpdf)) {
        require_once $manualFpdf;
    }
}

if (!class_exists('FPDF')) {
    http_response_code(500);
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><title>FPDF belum terpasang</title></head><body style="font-family:Arial,sans-serif;padding:30px">';
    echo '<h2>FPDF belum terpasang</h2>';
    echo '<p>Pasang FPDF terlebih dahulu dengan Composer:</p>';
    echo '<pre>composer require setasign/fpdf:^1.9</pre>';
    echo '<p>Setelah itu buka kembali tombol <b>Unduh PDF</b>.</p>';
    echo '</body></html>';
    exit;
}

$userId = userId();

$stDosen = $pdo->prepare("SELECT d.id, d.nama, d.nidn FROM dosen d JOIN users u ON u.username=d.nidn WHERE u.id=? LIMIT 1");
$stDosen->execute([$userId]);
$dosen = $stDosen->fetch(PDO::FETCH_ASSOC);

if (!$dosen) {
    http_response_code(403);
    exit('Data dosen tidak ditemukan.');
}

$did = (int)$dosen['id'];

$st = $pdo->prepare("\n    SELECT\n        s.id AS sesi_id,\n        s.tanggal,\n        s.pertemuan,\n        s.jam_mulai,\n        s.jam_selesai,\n        mk.nama_mk,\n        k.nama_kelas,\n        COUNT(DISTINCT km.mahasiswa_id) AS total,\n        COALESCE(SUM(CASE WHEN a.status IN ('Hadir','Terlambat') THEN 1 ELSE 0 END),0) AS hadir,\n        COALESCE(SUM(CASE WHEN a.status='Terlambat' THEN 1 ELSE 0 END),0) AS terlambat,\n        COALESCE(SUM(CASE WHEN a.status IN ('Izin','Sakit') THEN 1 ELSE 0 END),0) AS izin_sakit,\n        COALESCE(SUM(CASE WHEN a.status='Tidak Hadir' OR a.id IS NULL THEN 1 ELSE 0 END),0) AS tidak_hadir\n    FROM sesi_absensi s\n    JOIN jadwal j ON j.id=s.jadwal_id\n    JOIN mata_kuliah mk ON mk.id=j.mata_kuliah_id\n    JOIN kelas k ON k.id=j.kelas_id\n    JOIN kelas_mahasiswa km ON km.kelas_id=k.id\n    LEFT JOIN absensi a ON a.sesi_id=s.id AND a.mahasiswa_id=km.mahasiswa_id\n    WHERE j.dosen_id=?\n    GROUP BY s.id, s.tanggal, s.pertemuan, s.jam_mulai, s.jam_selesai, mk.nama_mk, k.nama_kelas\n    ORDER BY s.tanggal DESC, s.id DESC\n");
$st->execute([$did]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

function pdfText($value): string {
    $value = trim((string)$value);
    $value = str_replace(["\r", "\n", "\t"], ' ', $value);
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $value);
        if ($converted !== false) return $converted;
    }
    return preg_replace('/[^\x20-\x7E]/', '', $value) ?? '';
}

class AttendancePDF extends FPDF
{
    public function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 8, pdfText('LAPORAN PRESENSI'), 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, pdfText('Sistem Absensi QR'), 0, 1, 'C');
        $this->Ln(3);
        $this->SetDrawColor(150,150,150);
        $this->Line(10, 26, $this->GetPageWidth()-10, 26);
        $this->Ln(5);
    }

    public function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 6, pdfText('Halaman '.$this->PageNo()), 0, 0, 'C');
    }
}

$pdf = new AttendancePDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetTitle(pdfText('Laporan Presensi - '.$dosen['nama']));
$pdf->SetAuthor(pdfText($dosen['nama']));
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(28, 6, 'Dosen', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 6, ': '.pdfText($dosen['nama']), 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(28, 6, 'NIDN', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 6, ': '.pdfText($dosen['nidn']), 0, 1);
$pdf->Ln(4);

$widths = [27, 20, 62, 38, 22, 22, 25, 25, 29];
$headers = ['Tanggal','Pert.','Mata Kuliah','Kelas','Total','Hadir','Terlambat','Izin/Sakit','Tidak Hadir'];

$pdf->SetFillColor(235,235,235);
$pdf->SetDrawColor(160,160,160);
$pdf->SetFont('Arial', 'B', 8);
foreach ($headers as $i => $header) {
    $pdf->Cell($widths[$i], 8, pdfText($header), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
if (!$rows) {
    $pdf->Cell(array_sum($widths), 9, pdfText('Belum ada laporan presensi.'), 1, 1, 'C');
} else {
    foreach ($rows as $r) {
        $values = [
            $r['tanggal'],
            $r['pertemuan'] ?? '-',
            $r['nama_mk'],
            $r['nama_kelas'],
            $r['total'],
            $r['hadir'],
            $r['terlambat'],
            $r['izin_sakit'],
            $r['tidak_hadir'],
        ];
        foreach ($values as $i => $value) {
            $align = $i >= 4 ? 'C' : 'L';
            $pdf->Cell($widths[$i], 7, pdfText($value), 1, 0, $align);
        }
        $pdf->Ln();
    }
}

$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, pdfText('Dicetak: '.date('d-m-Y H:i').' WIB'), 0, 1, 'R');

$filename = 'laporan-presensi-'.preg_replace('/[^A-Za-z0-9_-]+/', '-', $dosen['nama']).'.pdf';
$pdf->Output('D', $filename);
exit;
