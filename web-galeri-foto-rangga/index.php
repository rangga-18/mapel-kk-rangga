
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Unik</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      height: 100vh;
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-box {
      background: rgba(255, 255, 255, 0.95);
      padding: 35px 25px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
      width: 340px;
      text-align: center;
      animation: slideIn 0.6s ease;
    }
    .login-box h2 {
      margin-bottom: 25px;
      color: #333;
    }
    .input-group {
      margin-bottom: 15px;
      text-align: left;
    }
    label {
      font-weight: bold;
      font-size: 14px;
      color: #555;
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 15px;
      transition: 0.3s;
    }
    input[type="text"]:focus, input[type="password"]:focus {
      border-color: #4facfe;
      box-shadow: 0 0 8px rgba(79,172,254,0.5);
      outline: none;
    }
    .btn {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #4facfe, #008cff);
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s;
    }
    .btn:hover {
      background: linear-gradient(135deg, #00c6ff, #0072ff);
      transform: scale(1.05);
    }
    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
    }
    @keyframes slideIn {
      from { transform: translateY(-40px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>🔐 Login Form</h2>

    <?php if (isset($_GET['error'])): ?>
      <div class="error"><?= $_GET['error']; ?></div>
    <?php endif; ?>

    <form action="proses_login.php" method="post">
      <div class="input-group">
        <label for="username">👤 Username</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="input-group">
        <label for="password">🔑 Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn">Login</button>
    </form>
  </div>
</body>
</html>
