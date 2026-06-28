package main

import (
    "context"
    "database/sql"
    "encoding/json"
    "fmt"
    "math"
    "net/http"
    "strconv"
    "time"
    _ "github.com/go-sql-driver/mysql"
)

var db *sql.DB

func compute() float64 {
    var x float64
    for i := 0; i < 1000; i++ {
        x += math.Sqrt(float64(i))
    }
    return x
}

func handler(w http.ResponseWriter, r *http.Request) {
    sleep := r.URL.Query().Get("sleep")
    sleepTime := 0.0
    if sleep != "" {
        sleepTime, _ = strconv.ParseFloat(sleep, 64)
    }

    x := compute()
    var dbOk interface{}

    // 带超时的查询
    ctx, cancel := context.WithTimeout(context.Background(), 2*time.Second)
    defer cancel()

    if sleepTime > 0 {
        err := db.QueryRowContext(ctx, "SELECT SLEEP(?)", sleepTime).Scan(&dbOk)
        if err != nil {
            dbOk = "error"
        }
    } else {
        err := db.QueryRowContext(ctx, "SELECT 1").Scan(&dbOk)
        if err != nil {
            dbOk = "error"
        }
    }

    resp := map[string]interface{}{"status":"ok","compute":x,"db":dbOk,"lang":"Go"}
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(resp)
}

func main() {
    var err error
    db, err = sql.Open("mysql", "bench_db:LbiAaSBhGHwXGcWx@tcp(127.0.0.1:3306)/bench_db")
    if err != nil {
        panic(err)
    }

    // 连接池优化
    db.SetMaxOpenConns(20)
    db.SetMaxIdleConns(20)
    db.SetConnMaxLifetime(5 * time.Minute)

    // 预热连接
    ctx, cancel := context.WithTimeout(context.Background(), 2*time.Second)
    defer cancel()
    var dummy int
    db.QueryRowContext(ctx, "SELECT 1").Scan(&dummy)

    http.HandleFunc("/", handler)
    fmt.Println("Go server on port 8080")
    http.ListenAndServe(":8080", nil)
}
