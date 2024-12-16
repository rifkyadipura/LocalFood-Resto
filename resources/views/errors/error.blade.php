<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="text-center">
        <h1 class="display-4 text-danger">{{ $title ?? 'Access Denied' }}</h1>
        <p class="lead">{{ $message ?? 'You do not have permission to access this page.' }}</p>
        <a href="{{ $redirectUrl ?? '/' }}" class="btn btn-primary mt-3">Go Back</a>
    </div>
</body>
</html>
