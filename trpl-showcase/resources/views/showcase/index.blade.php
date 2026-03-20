<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        :root {
            --bg: #0f172a;
            --card: #111827;
            --text: #e5e7eb;
            --muted: #9ca3af;
            --accent: #3b82f6;
            --accent-2: #1d4ed8;
            --border: #1f2937;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: radial-gradient(circle at top right, #1e3a8a 0%, var(--bg) 38%);
            color: var(--text);
            min-height: 100vh;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .title { font-size: 1.8rem; margin: 0; }
        .subtitle { color: var(--muted); margin: 6px 0 0 0; }
        .logout {
            border: 0;
            background: #ef4444;
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .card {
            background: rgba(17, 24, 39, .92);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            backdrop-filter: blur(4px);
        }
        .card h3 { margin: 0 0 8px 0; }
        .card p { margin: 0 0 16px 0; color: var(--muted); line-height: 1.5; }
        .open {
            display: inline-block;
            text-decoration: none;
            background: var(--accent);
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 600;
        }
        .open:hover { background: var(--accent-2); }
        .welcome {
            margin-top: 20px;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(15, 23, 42, .65);
            color: var(--muted);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1 class="title">{{ $title }}</h1>
            <p class="subtitle">Portal aplikasi TRPL terintegrasi dengan SSO Pusat</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout">Logout SSO</button>
        </form>
    </div>

    <div class="welcome">
        Login sebagai: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})
    </div>

    <div class="grid">
        @foreach($apps as $app)
            <article class="card">
                <h3>{{ $app['name'] }}</h3>
                <p>{{ $app['description'] }}</p>
                <a class="open" href="{{ $app['url'] }}" target="_blank" rel="noopener noreferrer">Buka Aplikasi</a>
            </article>
        @endforeach
    </div>
</div>
</body>
</html>
