<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Live Chat')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a2e;
            color: #e0e0e0;
            min-height: 100vh;
        }
        .navbar {
            background: #16213e;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .navbar h1 {
            font-size: 1.4rem;
            color: #00d2ff;
        }
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .navbar .user-info span {
            color: #a0a0a0;
        }
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00d2ff, #3a7bd5);
            color: white;
        }
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: #16213e;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #a0a0a0;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #2a3a5c;
            border-radius: 8px;
            background: #0f3460;
            color: #e0e0e0;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #00d2ff;
        }
        .error-list {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid #e74c3c;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 18px;
        }
        .error-list ul { list-style: none; }
        .error-list li { color: #e74c3c; font-size: 0.9rem; margin-bottom: 4px; }
        .text-center { text-align: center; }
        .mt-3 { margin-top: 15px; }
        a { color: #00d2ff; }
    </style>
    @yield('styles')
</head>
<body>
    @auth
    <nav class="navbar">
        <h1>💬 Live Chat</h1>
        <div class="user-info">
            <span>{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-danger" style="padding: 6px 14px; font-size: 0.8rem;">Logout</button>
            </form>
        </div>
    </nav>
    @endauth

    @yield('content')

    @yield('scripts')
</body>
</html>
