<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — WBMM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f8; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { margin-bottom: 1.5rem; text-align: center; color: #333; }
        .alert { padding: 0.75rem 1rem; border-radius: 4px; margin-bottom: 1rem; font-size: 0.9rem; }
        .alert-error { background: #fde8e8; color: #c0392b; border: 1px solid #f5c6cb; }
        .alert-success { background: #e8f5e9; color: #27ae60; border: 1px solid #c3e6cb; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.4rem; font-size: 0.9rem; color: #555; }
        input[type="email"], input[type="password"] { width: 100%; padding: 0.6rem 0.8rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
        input:focus { outline: none; border-color: #3498db; }
        button[type="submit"] { width: 100%; padding: 0.75rem; background: #3498db; color: #fff; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; }
        button[type="submit"]:hover { background: #2980b9; }
        .links { margin-top: 1rem; text-align: center; font-size: 0.85rem; }
        .links a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <h2>WBMM Login</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-error">
            <ul style="padding-left:1rem;">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/login" method="post">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="off" required>
        </div>

        <button type="submit">Log In</button>
    </form>

    <div class="links">
        <a href="/forgot-password">Forgot your password?</a>
    </div>
</div>
</body>
</html>
