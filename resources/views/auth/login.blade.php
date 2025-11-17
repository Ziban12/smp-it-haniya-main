<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; }
        .login-box {
            width: 350px; margin: 100px auto; padding: 20px;
            background: white; border-radius: 8px;
            box-shadow: 0px 0px 10px #ccc;
        }
        input { width: 100%; padding: 10px; margin-top: 10px; }
        button { width: 100%; padding: 10px; margin-top: 15px; background: #007bff; color: white; border: none; }
    </style>
</head>
<body>

<div class="login-box">
    <h3 style="text-align:center">School Login</h3>

    @if(session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif

    <form method="POST" action="/login">
        @csrf
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <button>Login</button>
    </form>
</div>

</body>
</html>
