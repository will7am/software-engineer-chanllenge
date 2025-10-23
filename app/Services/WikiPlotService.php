<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;


class WikiPlotService
{
    public function extractIfNumeric($value)
    {
        if (empty($value)) {
            return false;
        }

        $dateValue = strtotime($value);
        if ($dateValue != null) {
            return false;
        }

        if (preg_match('/[\d.]+/', $value, $matches)) {
            return (float) $matches[0];
        }

        return false;
    }


    public function parseContent($html)
    {
        try {
            $crawler = new Crawler($html);

            $table = $crawler->filter('table.wikitable')->first();
            if ($table->count() == 0) {
                return ['error' => 'No table value found'];
            }


            $headers = $table->filter('thead tr th')->each(function ($th) {
                return trim($th->text());
            });

            if (empty($headers) || empty(array_filter($headers))) {
                $firstRow = $table->filter('tbody tr')->first();
                if ($firstRow->count() > 0) {
                    $headers = $firstRow->filter('th, td')->each(function ($cell) {
                        return trim($cell->text());
                    });
                }
            }

            $rows = $table->filter('tbody tr')->each(function ($tr) use ($headers) {
                $cells = $tr->filter('td')->each(function ($cell) {
                    $isNumeric = $this->extractIfNumeric($cell->text());
                    if ($isNumeric) {
                        return $isNumeric;
                    }
                    return trim($cell->text());
                });
                if (count($headers) == count($cells)) {
                    return array_combine($headers, $cells);
                }

                return $cells;
            });

            $rows = array_filter($rows, function ($row) {
                return !empty($row) && !empty(array_filter($row));
            });

            return [
                'headers' => $headers,
                'rows' => array_values($rows),
            ];

            return $parsedTable;
        } catch (\Exception $e) {
            return ['error' => 'Error parsing content: ' . $e->getMessage()];
        }
    }

    public function identifyNumericColumn($tableData)
    {
        if (empty($tableData['rows'])) {
            throw new \Exception('No table data detected');
        }

        $index = '';
        $row = $tableData['rows'][0];

        /** Only find the first numeric column is enough for this solution */
        foreach ($row as $key => $value) {
            if (is_numeric($value)) {
                $index = $key;
                break;
            }
        }
        $numericColumn = ['index' => $index, 'data' => array_column($tableData['rows'], $index)];
        return $numericColumn;
    }

   

    public function createPlotImage($tableData)
    {
        /** The standard example of generating image */
        $values = $tableData['data'];
        $columnName = $tableData['index'];

        $width = 1000;
        $height = 600;
        $margin = 80;

        // Create image
        $image = imagecreate($width, $height);

        // Define colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $red = imagecolorallocate($image, 255, 0, 0);
        $blue = imagecolorallocate($image, 0, 0, 255);
        $gray = imagecolorallocate($image, 200, 200, 200);
        $darkGray = imagecolorallocate($image, 100, 100, 100);

        // Fill background
        imagefill($image, 0, 0, $white);

        $chartWidth = $width - ($margin * 2);
        $chartHeight = $height - ($margin * 2);

        // Draw grid lines
        for ($i = 0; $i <= 10; $i++) {
            $y = $margin + ($i * $chartHeight / 10);
            imageline($image, $margin, $y, $width - $margin, $y, $gray);
        }

        // Draw axes
        imageline($image, $margin, $margin, $margin, $height - $margin, $black);
        imageline($image, $margin, $height - $margin, $width - $margin, $height - $margin, $black);

        // Calculate data range
        $maxValue = max($values);
        $minValue = min($values);
        $valueRange = $maxValue - $minValue ?: 1;
        $pointCount = count($values);

        $points = [];

        // Draw data points and lines
        for ($i = 0; $i < $pointCount; $i++) {
            $x = $margin + ($i * $chartWidth / ($pointCount - 1));
            $y = $margin + $chartHeight - (($values[$i] - $minValue) / $valueRange * $chartHeight);

            $points[] = ['x' => $x, 'y' => $y];

            // Draw data point
            imagefilledellipse($image, $x, $y, 8, 8, $red);
            imageellipse($image, $x, $y, 8, 8, $black);

            // Draw value label (every few points to avoid clutter)
            if ($i % max(1, floor($pointCount / 8)) === 0) {
                imagestring($image, 2, $x + 10, $y - 12, round($values[$i], 2), $darkGray);
            }
        }

        // Draw connecting lines
        for ($i = 0; $i < $pointCount - 1; $i++) {
            imageline(
                $image,
                $points[$i]['x'],
                $points[$i]['y'],
                $points[$i + 1]['x'],
                $points[$i + 1]['y'],
                $blue
            );
        }

        // Add title
        $title = "Wikipedia Data Plot: " . substr($columnName, 0, 40);
        imagestring($image, 5, $width / 2 - (strlen($title) * 7), 30, $title, $black);

        // Add axis labels
        $xLabel = "Data Points Sequence";
        imagestring($image, 4, $width / 2 - (strlen($xLabel) * 6), $height - 40, $xLabel, $black);

        $yLabel = "Numeric Values";
        // Vertical text for Y-axis (simple approach)
        imagestringup($image, 4, 30, $height / 2 + (strlen($yLabel) * 6), $yLabel, $black);

        // Add data statistics
        $stats = "Data Points: " . $pointCount . " | Range: " . round($minValue, 2) . " - " . round($maxValue, 2);
        imagestring($image, 3, $width - 300, 50, $stats, $darkGray);

        // Ensure plots directory exists
        $plotsDir = storage_path('app/public/plots');
        if (!is_dir($plotsDir)) {
            mkdir($plotsDir, 0755, true);
        }

        // Save image
        $filename = 'wiki_plot_' . time() . '.png';
        $filepath = $plotsDir . '/' . $filename;
        imagepng($image, $filepath);
        imagedestroy($image);

        return $filename;
    }
}
