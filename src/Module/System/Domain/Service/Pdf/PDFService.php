<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

final class PDFService
{
    public function __construct(private Environment $twig)
    {
    }

    public function generatePdf(string $template, array $data = []): string
    {
        $html = $this->twig->render($template, $data);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $dompdf->output();
    }

    public function streamPdf(string $template, array $data = []): string
    {
        return $this->generatePdf($template, $data);
    }

    public function savePdfToFile(string $template, array $data, string $path = '/storage/file.pdf'): void
    {
        $pdfContent = $this->generatePdf($template, $data);
        file_put_contents($path, $pdfContent);
    }
}
