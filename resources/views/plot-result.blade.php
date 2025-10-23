<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plot Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-light py-5">

    <div class="container text-center">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h3 class="text-success fw-bold">Plot Generated Successfully</h3>
                <p class="text-muted mb-2">
                    Data Source:
                    <a href="{{ $sourceUrl }}" target="_blank" class="text-decoration-none text-primary">
                        {{ $sourceUrl }}
                    </a>
                </p>
                <div class="mt-3">
                    <a href="{{ route('tableParser.parsePage') }}" class="btn btn-secondary me-2"><i class="fa-solid fa-arrow-left"></i>  Back to Parse</a>
                    <a href="{{ asset('storage/plots/' . $imagePath) }}"
                        download="wiki_plot.png"
                        class="btn btn-primary fw"><i class="fa-solid fa-download"></i> Download as Image</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="text-primary mb-3">Generated Plot</h5>
                <img src="{{ asset('storage/plots/' . $imagePath) }}"
                    alt="Wikipedia Data Plot"
                    class="img-fluid rounded shadow">
                <p class="text-muted mt-3 small">
                    Plot of <strong>{{ $numericColumnData['index'] }}</strong> ({{ count($numericColumnData['data']) }} data points)
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>