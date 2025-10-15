<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $subject ?? 'Notifikasi' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fafafa;
            max-width: 600px;
            margin: auto;
        }
        h2 {
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><?= $subject ?? 'Informasi' ?></h2>
    <p>Halo <b><?= $nama ?? 'Pengguna' ?></b>,</p>

    <p><?= $pesan ?? 'Ini adalah email otomatis dari sistem kami.' ?></p>

    <p>
        Salam hangat,<br>
        <b>PAR-MA</b>
    </p>
</div>
</body>
</html>
