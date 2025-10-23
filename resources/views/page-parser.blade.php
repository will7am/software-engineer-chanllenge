<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wikipedia Table Paser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-sm p-4" style="max-width: 500px; width: 100%;">
        <div class="card-body text-center">
            <h3 class="mb-3 text-primary fw-bold">Software Engineer Challenge - Candidate William Sun</h3>
            <p class="text-muted mb-4">Enter a Wikipedia URL to extract numeric data and plot.</p>
            <br><br>

            <form action="{{ route('tableParser.plotGraph') }}" method="POST" class="text-start">
                @csrf
                <div class="mb-3">
                    <label for="url" class="form-label fw-semibold"><i class="fa-solid fa-link"></i>  Wikipedia URL: </label>
                    <input type="text"
                           id="url"
                           name="url"
                           class="form-control"
                           placeholder="Enter Wikipedia URL"
                           value="https://en.wikipedia.org/wiki/Women%27s_high_jump_world_record_progression"
                           required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary fw-semibold">
                    <i class="fa fa-solid fa-magnifying-glass-chart"></i>  Click to Parse
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
