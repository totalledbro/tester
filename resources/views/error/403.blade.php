<!-- resources/views/errors/403.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
        }
        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: #dc3545;
        }
        p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            h1 {
                font-size: 36px;
            }
            p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>403 Forbidden</h1>
        <p>Sorry, you are not allowed to access this page.</p>
        <p><a href="{{ route('dash') }}">Go back to Dashboard</a></p>
    </div>
</body>
</html>
