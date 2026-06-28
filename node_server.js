const http = require('http');
const mysql = require('mysql2');
const url = require('url');

const pool = mysql.createPool({
    host: '127.0.0.1',
    user: 'bench_db',
    password: 'LbiAaSBhGHwXGcWx',
    database: 'bench_db',
    waitForConnections: true,
    connectionLimit: 20,
    queueLimit: 0
});

function compute() {
    let x = 0;
    for (let i = 0; i < 1000; i++) x += Math.sqrt(i);
    return x;
}

const server = http.createServer((req, res) => {
    const parsedUrl = url.parse(req.url, true);
    const sleepTime = parseFloat(parsedUrl.query.sleep) || 0;

    const x = compute();
    pool.getConnection((err, conn) => {
        if (err) {
            res.writeHead(500);
            res.end(JSON.stringify({status:'error', db:'error'}));
            return;
        }
        let query = 'SELECT 1';
        let params = [];
        if (sleepTime > 0) {
            query = 'SELECT SLEEP(?) as ok';
            params = [sleepTime];
        }
        conn.query(query, params, (err, results) => {
            conn.release();
            const dbOk = err ? 'error' : (results[0]?.ok || results[0]?.['1'] || 1);
            res.writeHead(200, {'Content-Type': 'application/json'});
            res.end(JSON.stringify({status:'ok', compute:x, db:dbOk, lang:'Node.js'}));
        });
    });
});
server.listen(3000, () => console.log('Node.js server on port 3000'));
