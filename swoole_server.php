<?php
use Swoole\Http\Server;

$server = new Server("0.0.0.0", 9501);
$server->set([
    'worker_num' => 20,
    'enable_coroutine' => true,
    'hook_flags' => SWOOLE_HOOK_ALL,
]);

$server->on('request', function($req, $res) {
    $sleepTime = isset($req->get['sleep']) ? (float)$req->get['sleep'] : 0;
    $x = 0;
    for ($i = 0; $i < 1000; $i++) $x += sqrt($i);
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=bench_db', 'bench_db', 'LbiAaSBhGHwXGcWx');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($sleepTime > 0) {
            $stmt = $pdo->prepare("SELECT SLEEP(?) as ok");
            $stmt->execute([$sleepTime]);
            $dbOk = $stmt->fetchColumn();
        } else {
            $stmt = $pdo->query("SELECT 1");
            $dbOk = $stmt->fetchColumn();
        }
    } catch (Exception $e) {
        $dbOk = 'error';
    }
    $res->header('Content-Type', 'application/json');
    $res->end(json_encode(['status'=>'ok','compute'=>$x,'db'=>$dbOk,'lang'=>'PHP+Swoole','sleep'=>$sleepTime]));
});
$server->start();
