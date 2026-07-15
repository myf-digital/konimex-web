<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $subject ?? 'Notifikasi' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        .container {
            width: 90%;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fafafa;
            margin: 20px auto;
            box-sizing: border-box;
        }
        .container-no-border {
            width: 90%;
            padding: 20px;
            border: 1px solid #fff;
            border-radius: 6px;
            background: #fff;
            margin: 20px auto;
            box-sizing: border-box;
        }
        h2 {
            color: #007bff;
            margin-top: 0;
        }
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }
        }
        @media (max-width: 480px) {
            .container {
                width: 100%;
                margin: 0;
                border-radius: 0;
                border: none;
            }
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 10pt;
        }
        th, td {
            border: 0.2px solid #444;
            padding: 4px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #fcfcfc;
        }
    </style>
</head>
<body>
<div class="<?= isset($is_pdf) && $is_pdf ? 'container-no-border' : 'container' ?>">
    <h2><?= $subject ?? 'Informasi' ?></h2>
    <p>Halo <b><?= $name ?? 'Pengguna' ?></b>,</p>

    <p><?= $message ?? 'Ini adalah email otomatis dari sistem kami.' ?></p>

    <p>
        Salam hangat,<br>
        <b>HIMALAYA</b>
    </p>
</div>
</body>
</html>
