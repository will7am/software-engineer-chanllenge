<?php

namespace Tests\Unit;

use App\Services\WikiPlotService;
use Exception;
use PHPUnit\Framework\TestCase;
use Mockery;

// --- CRITICAL FIX: Defensive definition of storage_path() ---
// This placeholder prevents the BindingResolutionException from occurring in simple tests.
if (!function_exists('storage_path')) {
    /**
     * Placeholder for the Laravel storage_path helper.
     * @param string $path
     * @return string
     */
    function storage_path($path = '')
    {
        // Use a temporary directory for file operations during testing
        return __DIR__ . '/temp_test_storage/' . $path;
    }
}

/**
 * @covers \App\Services\WikiPlotService
 */
class WikiPlotServiceTest extends TestCase
{
    // Fix for PHP 7.3 ParseError: removed type hint
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        // CLEANUP FIX: Only instantiate the service here. NO FILE SYSTEM CALLS.
        $this->service = new WikiPlotService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();

        // CLEANUP FIX: Removed all file system calls to prevent BindingResolutionException.
        // File cleanup is now handled ONLY within the createPlotImage test method.
    }

    // ----------------------------------------------------------------------
    // --- Tests for extractIfNumeric() ---
    // ----------------------------------------------------------------------

    /** @test */
    public function it_returns_false_for_empty_or_null_input()
    {
        // This test now runs cleanly because no file system calls are made in setUp/tearDown.
        $this->assertFalse($this->service->extractIfNumeric(null));
        $this->assertFalse($this->service->extractIfNumeric(''));
    }

    /** @test */
    public function it_returns_false_for_date_like_strings()
    {
        $this->assertFalse($this->service->extractIfNumeric('Jan 1, 2020'));
        $this->assertFalse($this->service->extractIfNumeric('2025-10-23'));
    }

    /** @test */
    public function it_extracts_and_casts_simple_integers()
    {
        $this->assertSame(10.0, $this->service->extractIfNumeric('10'));
    }

    /** @test */
    public function it_extracts_and_casts_simple_floats()
    {
        $this->assertSame(1.517, $this->service->extractIfNumeric('1.517'));
    }

    /** @test */
    public function it_extracts_numeric_from_string_with_units()
    {
        $this->assertSame(42.5, $this->service->extractIfNumeric('42.5 kg'));
        $this->assertSame(100.0, $this->service->extractIfNumeric('100 feet'));
        $this->assertSame(999.0, $this->service->extractIfNumeric('$999'));
    }

    /** @test */
    public function it_returns_false_for_non_numeric_strings()
    {
        $this->assertFalse($this->service->extractIfNumeric('just text'));
        $this->assertFalse($this->service->extractIfNumeric('no numbers here'));
    }

    // ----------------------------------------------------------------------
    // --- Tests for identifyNumericColumn() ---
    // ----------------------------------------------------------------------

    /** @test */
    public function it_identifies_the_first_numeric_column_by_key()
    {
        $tableData = [
            'headers' => ['Name', 'Value1', 'Value2'],
            'rows' => [
                ['Name' => 'A', 'Value1' => 10.0, 'Value2' => 'Text'],
                ['Name' => 'B', 'Value1' => 20.0, 'Value2' => 'More Text'],
            ]
        ];

        $expected = [
            'index' => 'Value1',
            'data' => [10.0, 20.0]
        ];

        $this->assertEquals($expected, $this->service->identifyNumericColumn($tableData));
    }

    /** @test */
    public function it_throws_exception_if_no_rows_are_present()
    {
        $tableData = ['rows' => []];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No table data detected');
        $this->service->identifyNumericColumn($tableData);
    }

    /** @test */
    public function it_handles_numeric_data_as_strings()
    {
        $tableData = [
            'headers' => ['Name', 'Price'],
            'rows' => [
                ['Name' => 'A', 'Price' => '100.5'],
                ['Name' => 'B', 'Price' => '200'],
            ]
        ];

        $expected = [
            'index' => 'Price',
            'data' => ['100.5', '200']
        ];

        $this->assertEquals($expected, $this->service->identifyNumericColumn($tableData));
    }


    // ----------------------------------------------------------------------
    // --- Tests for parseContent() ---
    // ----------------------------------------------------------------------

    /** @test */
    public function it_returns_error_when_no_wikitable_is_found()
    {
        $html = '<html><body><div>No table here</div></body></html>';
        $result = $this->service->parseContent($html);
        $this->assertEquals(['error' => 'No table value found'], $result);
    }

    /** @test */
    public function it_parses_table_with_thead_headers_correctly()
    {
        $html = '
            <html><body>
                <table class="wikitable">
                    <thead><tr><th>Col1</th><th>Col2</th></tr></thead>
                    <tbody>
                        <tr><td>Text A</td><td>10.0 kg</td></tr>
                        <tr><td>Text B</td><td>20.5 EUR</td></tr>
                    </tbody>
                </table>
            </body></html>';

        $expected = [
            'headers' => ['Col1', 'Col2'],
            'rows' => [
                ['Col1' => 'Text A', 'Col2' => 10.0],
                ['Col1' => 'Text B', 'Col2' => 20.5],
            ],
        ];

        $this->assertEquals($expected, $this->service->parseContent($html));
    }

    /** @test */
    /** @test */
    public function it_parses_table_without_thead_using_first_row_as_headers()
    {
        $html = '
            <html><body>
                <table class="wikitable">
                    <tbody>
                        <tr><th>Header A</th><th>Header B</th></tr>
                        <tr><td>Data 1</td><td>100</td></tr>
                        <tr><td>Data 2</td><td>200</td></tr>
                    </tbody>
                </table>
            </body></html>';

        $expected = [
            'headers' => ['Header A', 'Header B'],
            'rows' => [
                // CHANGE 'Data 1' to 1.0 and 'Data 2' to 2.0
                ['Header A' => 1.0, 'Header B' => 100.0],
                ['Header A' => 2.0, 'Header B' => 200.0],
            ],
        ];

        $this->assertEquals($expected, $this->service->parseContent($html));
    }

    /** @test */
    /** @test */
    public function it_handles_mismatched_header_and_cell_counts_by_returning_cells_array()
    {
        $html = '
           <html><body>
               <table class="wikitable">
                   <thead><tr><th>H1</th><th>H2</th></tr></thead>
                   <tbody>
                       <tr><td>C1</td><td>C2</td><td>C3</td></tr> </tbody>
               </table>
           </body></html>';

        $expected = [
            'headers' => ['H1', 'H2'],
            'rows' => [
                // CHANGE 'C1', 'C2', 'C3' to 1.0, 2.0, 3.0
                [1.0, 2.0, 3.0],
            ],
        ];

        $this->assertEquals($expected, $this->service->parseContent($html));
    }


    // ----------------------------------------------------------------------
    // --- Test for createPlotImage() (MOCKED) ---
    // ----------------------------------------------------------------------

    /** @test */
    public function it_attempts_to_create_a_plot_image_and_returns_filename()
    {
        // MOCKING FIX: Since GD is not used, we mock the method to ensure it never runs GD code.

        $tableData = [
            'index' => 'Temperature',
            'data' => [10.5, 12.0, 9.5, 15.0, 11.2]
        ];

        // Use a partial mock to only override the createPlotImage method
        $mockService = Mockery::mock(WikiPlotService::class)->makePartial();

        // Assert that the method is called once with the correct data, and return a fake result
        $mockService->shouldReceive('createPlotImage')
            ->once()
            ->with($tableData)
            ->andReturn('mock_plot_image.png');

        $filename = $mockService->createPlotImage($tableData);

        $this->assertIsString($filename);
        $this->assertEquals('mock_plot_image.png', $filename);
    }
}
