import asyncio
import math
import json
from aiohttp import web
import aiomysql

pool = None

async def init_pool():
    global pool
    pool = await aiomysql.create_pool(
        host='127.0.0.1',
        port=3306,
        user='bench_db',
        password='LbiAaSBhGHwXGcWx',
        db='bench_db',
        minsize=5,
        maxsize=20,
        loop=asyncio.get_event_loop()
    )

def compute():
    x = 0.0
    for i in range(1000):
        x += math.sqrt(i)
    return x

async def handle(request):
    sleepTime = float(request.query.get('sleep', 0))

    x = compute()
    async with pool.acquire() as conn:
        async with conn.cursor() as cur:
            if sleepTime > 0:
                await cur.execute("SELECT SLEEP(%s)", (sleepTime,))
                dbOk = (await cur.fetchone())[0]
            else:
                await cur.execute("SELECT 1")
                dbOk = (await cur.fetchone())[0]
    return web.json_response({'status':'ok','compute':x,'db':dbOk,'lang':'Python'})

app = web.Application()
app.router.add_get('/', handle)
app.router.add_get('/{name}', handle)

async def main():
    await init_pool()
    runner = web.AppRunner(app)
    await runner.setup()
    site = web.TCPSite(runner, '0.0.0.0', 5000)
    await site.start()
    print("Python server on port 5000")
    await asyncio.Event().wait()

if __name__ == '__main__':
    asyncio.run(main())
