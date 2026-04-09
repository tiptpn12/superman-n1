<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .email-header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }

        .email-body {
            padding: 20px;
            color: #333333;
        }

        .email-body p {
            margin: 0 0 10px;
        }

        .reset-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }

        .reset-button:hover {
            background-color: #0056b3;
        }

        .email-footer {
            text-align: center;
            padding: 10px;
            background-color: #f4f4f4;
            font-size: 12px;
            color: #666666;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            Reset Password
        </div>
        <div class="email-body">
            <p>Halo,</p>
            <p>Kami menerima permintaan untuk mereset password akun Anda. Jika Anda tidak melakukan permintaan ini,
                abaikan email ini.</p>
            <p>Untuk mengatur ulang password Anda, klik tombol di bawah ini:</p>
            <a href="{{ route('reset_password', ['token' => $token, 'email' => $email]) }}" class="reset-button">Atur Ulang Password</a>
            <p>Link ini berlaku selama 15 Menit.</p>
            <p>Terima kasih,</p>
            <p><strong>Tim TI</strong></p>
        </div>
        <div class="email-footer">
            &copy; 2024 PT. Perkebunan Nusantara I. Semua Hak Dilindungi.
        </div>
    </div>
</body>

</html>
