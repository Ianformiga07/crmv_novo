<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/conexao.php';
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Acesso Negado — CRMV/TO</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#f1f4f8;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.1);padding:48px 40px;text-align:center;max-width:420px;width:100%}
.icon{width:72px;height:72px;border-radius:50%;background:#fee2e2;color:#dc2626;font-size:1.8rem;display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
h1{font-family:'Playfair Display',serif;font-size:1.5rem;color:#0f172a;margin-bottom:8px}
p{color:#64748b;font-size:.88rem;line-height:1.6;margin-bottom:24px}
.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#15385c;color:#fff;border-radius:8px;font-size:.88rem;font-weight:500;text-decoration:none}
.btn:hover{background:#0d2137}
</style>
</head>
<body>
<div class="card">
    <div class="icon"><i class="fa-solid fa-lock"></i></div>
    <h1>Acesso Negado</h1>
    <p>Você não tem permissão para acessar esta página.<br>
       Se acredita que isso é um erro, entre em contato com a administração.</p>
    <a href="/crmv/login.php" class="btn">
        <i class="fa-solid fa-arrow-left"></i> Voltar ao Login
    </a>
</div>
</body>
</html>
