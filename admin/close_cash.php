<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

include '../connection.php';

// Bugünün tarihini alma
$today = date('Y-m-d');

// Günlük siparişleri ve toplam hasılatı sorgulama
$sql = "SELECT COUNT(order_id) as total_orders, SUM(payment) as total_revenue 
        FROM orders 
        WHERE DATE(created_at) = '$today'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

// Siparişlerin gerçek tutarını hesaplama
$sql_details = "SELECT od.order_id, od.menu_id, od.piece, mi.price 
                FROM order_details od
                JOIN menu_items mi ON od.menu_id = mi.menu_id
                JOIN orders o ON od.order_id = o.order_id
                WHERE DATE(o.created_at) = '$today'";
$result_details = $conn->query($sql_details);

$total_actual_revenue = 0;

while($row = $result_details->fetch_assoc()) {
    $total_actual_revenue += $row['piece'] * $row['price'];
}

// Günlük indirimi hesaplama
$discount = $total_actual_revenue - $data['total_revenue'];

// Kart ve nakit ödemelerini ayrı ayrı hesaplama
$sql_payments = "SELECT payment_type, SUM(payment) as total_payment 
                 FROM orders 
                 WHERE DATE(created_at) = '$today'
                 GROUP BY payment_type";
$result_payments = $conn->query($sql_payments);

$cash_total = 0;
$card_total = 0;

while($row = $result_payments->fetch_assoc()) {
    if ($row['payment_type'] == 1) {
        $cash_total = $row['total_payment'];
    } elseif ($row['payment_type'] == 2) {
        $card_total = $row['total_payment'];
    }
}

// Dosya adı ve yolu
$filename = 'gunluk_rapor.xlsx';

// Var olan dosyayı yükleme veya yeni dosya oluşturma
if (file_exists($filename)) {
    $spreadsheet = IOFactory::load($filename);
    $sheet = $spreadsheet->getActiveSheet();
    $row = $sheet->getHighestRow() + 1;
} else {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $row = 2;
    // Başlıklar
    $sheet->setCellValue('A1', 'Tarih');
    $sheet->setCellValue('B1', 'Toplam Sipariş');
    $sheet->setCellValue('C1', 'Toplam Hasılat');
    $sheet->setCellValue('D1', 'Gerçek Hasılat');
    $sheet->setCellValue('E1', 'Yapılan İndirim');
    $sheet->setCellValue('F1', 'Nakit Toplam');
    $sheet->setCellValue('G1', 'Kart Toplam');
}

// Veriler
$sheet->setCellValue('A'.$row, $today);
$sheet->setCellValue('B'.$row, $data['total_orders']);
$sheet->setCellValue('C'.$row, $data['total_revenue']);
$sheet->setCellValue('D'.$row, $total_actual_revenue);
$sheet->setCellValue('E'.$row, $discount);
$sheet->setCellValue('F'.$row, $cash_total);
$sheet->setCellValue('G'.$row, $card_total);

// Excel dosyasını kaydetme
$writer = new Xlsx($spreadsheet);
$writer->save($filename);

$file = 'gunluk_rapor.xlsx';
// Dosya mevcut mu kontrol edin
if (file_exists($file)) {
    // Spreadsheet nesnesi oluşturun
    $spreadsheet = IOFactory::load($file);

    // Dosya ismi
    $fileName = basename($file);

    // Header'ları ayarlayın
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    // Writer oluşturun ve çıktı verin
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} else {
    echo "Dosya bulunamadı.";
}

header("Location: admin.php");

// Veritabanı bağlantısını kapatma
$conn->close();
?>
