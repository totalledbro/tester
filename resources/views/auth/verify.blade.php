<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Berhasil</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 360px;
            text-align: center;
        }
        .container h2 {
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 24px;
            color: #333;
        }
        p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }
    </style>
    <script>
        setTimeout(function() {
            window.location.href = "{{ route('dash') }}";
        }, 3000);
    </script>
</head>
<body>
    <div class="container">
        <h2>Verifikasi Berhasil</h2>
        <p>Email anda telah berhasil diverifikasi. Anda akan diarahkan ke dashboard.</p>
    </div>
</body>
</html>
