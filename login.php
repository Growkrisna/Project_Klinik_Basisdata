<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Klinik Sehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 28px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo h1 { font-size: 1.75rem; color: #0f766e; }
        .logo p { font-size: 0.875rem; color: #64748b; margin-top: 4px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 6px; color: #334155; }
        select, input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            font-size: 0.875rem;
            font-family: inherit;
        }
        select:focus, input:focus { outline: none; border-color: #0f766e; box-shadow: 0 0 0 3px rgba(15,118,110,0.1); }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #0f766e;
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
        }
        .btn-login:hover { background: #0d9488; }
        .demo-info { text-align: center; margin-top: 24px; font-size: 0.7rem; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <div style="font-size: 48px;">🏥</div>
            <h1>Klinik Sehat</h1>
            <p>Sistem Informasi Klinik Rawat Jalan</p>
        </div>
        <form method="POST" action="dashboard.php">
            <div class="form-group">
                <label>Pilih Peran</label>
                <select name="role" required>
                    <option value="">-- Pilih --</option>
                    <option value="pendaftaran">📋 Petugas Pendaftaran</option>
                    <option value="dokter">👨‍⚕️ Dokter</option>
                    <option value="apoteker">💊 Apoteker</option>
                    <option value="kasir">💰 Kasir</option>
                </select>
            </div>
            <div class="form-group">
                <label>Username (Demo)</label>
                <input type="text" name="username" placeholder="Ketik apa saja" required>
            </div>
            <div class="form-group">
                <label>Password (Demo)</label>
                <input type="password" name="password" placeholder="Ketik apa saja" required>
            </div>
            <button type="submit" class="btn-login">Login →</button>
        </form>
        <div class="demo-info">
            Demo: Pilih role lalu login (password bebas)
        </div>
    </div>
</body>
</html>