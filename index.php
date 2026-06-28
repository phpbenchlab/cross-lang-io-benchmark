<?php
$sleepTime = isset($_GET['sleep']) ? (float)$_GET['sleep'] : 0;

$x = 0;
for ($i = 0; $i < 1000; $i++) $x += sqrt($i);

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=bench_db', 'bench_db', 'LbiAaSBhGHwXGcWx');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($sleepTime > 0) {
        $stmt = $pdo->prepare("SELECT SLEEP(:sleep) as ok");
        $stmt->execute(['sleep' => $sleepTime]);
        $dbOk = $stmt->fetchColumn();
    } else {
        $dbOk = $pdo->query("SELECT 1")->fetchColumn();
    }
} catch (Exception $e) {
    $dbOk = 'error';
}
header('Content-Type: application/json');
echo json_encode(['status'=>'ok','compute'=>$x,'db'=>$dbOk,'lang'=>'PHP-FPM']);
