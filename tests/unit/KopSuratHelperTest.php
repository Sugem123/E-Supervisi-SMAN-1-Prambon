<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Router\DefinedRouteCollector;

/**
 * @internal
 */
final class KopSuratHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('setting');
    }

    public function testGetKopDataReturnsExpectedKeysAndDefaults(): void
    {
        $kop = get_kop_data();

        $this->assertIsArray($kop);
        $this->assertArrayHasKey('baris_1', $kop);
        $this->assertArrayHasKey('baris_2', $kop);
        $this->assertArrayHasKey('baris_3', $kop);
        $this->assertArrayHasKey('baris_4', $kop);
        $this->assertArrayHasKey('baris_5', $kop);
        $this->assertArrayHasKey('logo_kiri', $kop);
        $this->assertArrayHasKey('tampilkan_logo', $kop);
        $this->assertArrayHasKey('tampilkan_garis', $kop);

        $this->assertNotEmpty($kop['baris_1']);
        $this->assertNotEmpty($kop['baris_3']);
    }

    public function testRenderKopSuratOutputsValidHtmlWithDoubleBorder(): void
    {
        $html = render_kop_surat();

        $this->assertIsString($html);
        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('border: 0px none transparent !important', $html);
        $this->assertStringContainsString('border-top: 2px solid #000; border-bottom: 1px solid #000;', $html);
        $this->assertStringContainsString('KEMENTERIAN AGAMA', $html);
    }

    public function testRenderKopSuratWithCustomOverrides(): void
    {
        $custom = [
            'baris_1' => 'YAYASAN PENDIDIKAN ISLAM',
            'baris_2' => 'KANTOR WILAYAH LAMPUNG',
            'baris_3' => 'MADRASAH HEBAT BERMARTABAT',
            'baris_4' => 'Jl. Pendidikan No. 1',
            'baris_5' => 'Email: madrasah@example.com',
            'tampilkan_garis' => '0',
        ];

        $html = render_kop_surat($custom);

        $this->assertStringContainsString('YAYASAN PENDIDIKAN ISLAM', $html);
        $this->assertStringContainsString('MADRASAH HEBAT BERMARTABAT', $html);
        $this->assertStringNotContainsString('border-top: 2px solid #000;', $html);
    }

    public function testUpdateKopRouteExists(): void
    {
        $collection = service('routes')->loadRoutes();
        $definedRoutes = iterator_to_array((new DefinedRouteCollector($collection))->collect());

        $kopRoutes = array_filter($definedRoutes, function ($r) {
            return str_contains($r['route'], 'update-kop');
        });

        $this->assertNotEmpty($kopRoutes, 'Route update-kop should exist');

        $methods = array_map('strtoupper', array_column($kopRoutes, 'method'));
        $this->assertContains('POST', $methods);
    }

    public function testDompdfRendersKopWithoutErrors(): void
    {
        $html = '<!DOCTYPE html><html><head><style>table, th, td { border: 1px solid #000; }</style></head><body>' . render_kop_surat() . '</body></html>';
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF', $output);
    }
}
