<!DOCTYPE html>
<html>
<head><title>Staff Dashboard</title></head>
<body>
    <h1>Halaman STAFF 🛠️</h1>
    <p>Halo, {{ Auth::user()->name }}!</p>
    
    <a href="{{ route('logout') }}">LOGOUT</a>
</body>
</html>