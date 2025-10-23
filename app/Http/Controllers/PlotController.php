<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\WikiPlotService;


class PlotController extends Controller
{
    private $wikiPlotService;

    public function __construct(WikiPlotService $wikiPlotService)
    {
        $this->wikiPlotService = $wikiPlotService;
    }

    public function plotGraph(Request $request)
    {
        $url = $request->input('url');

        if (empty($url)) {
            return response()->json(['error' => 'Please provide a valid URL'], 400);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Chanllenger/1.0',
            ])->timeout(30)->get($url);

            if (!$response->ok()) {
                return response()->json(['error' => 'Failed to fetch URL. Status: ' . $response->status()], 400);
            }

            $html = $response->body();
            $tableData = $this->wikiPlotService->parseContent($html);
            $numericColumnData = $this->wikiPlotService->identifyNumericColumn($tableData);

            $imagePath = $this->wikiPlotService->createPlotImage($numericColumnData);

            session([
                'plot_data' => $tableData,
                'image_path' => $imagePath,
                'source_url' => $url
            ]);

            return redirect()->route('plot.result');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function showResult()
    {

        if (!session('plot_data')) {
            return redirect('/');
        }

        $tableData = session('plot_data');
        $numericColumnData = $this->wikiPlotService->identifyNumericColumn($tableData);

        return view('plot-result', [
            'tableData' => $tableData,
            'numericColumnData' => $numericColumnData,
            'imagePath' => session('image_path'),
            'sourceUrl' => session('source_url')
        ]);
    }

    public function downloadPlot($filename)
    {
        $path = storage_path('app/public/plots/' . $filename);
        
        if (!file_exists($path)) {
            abort(404, 'Plot image not found');
        }

        return response()->download($path);
    }
}
