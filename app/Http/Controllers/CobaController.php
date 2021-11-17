<?php
use setasign\Fpdi;

$parser = 'default';
$filename = 'Normal.pdf';

require_once('fpdf/fpdf.php');
require_once('fpdi2/src/autoload.php');
require_once('fpdi_pdf-parser2/src/autoload.php');

// overwrite FPDI to define which parser should be used.
class Pdf extends Fpdi\Fpdi
{
    /**
     * @var string
     */
    protected $pdfParserClass = null;

    /**
     * Set the pdf reader class.
     *
     * @param string $pdfParserClass
     */
    public function setPdfParserClass($pdfParserClass)
    {
        $this->pdfParserClass = $pdfParserClass;
    }

    /**
     * Get a new pdf parser instance.
     *
     * @param Fpdi\PdfParser\StreamReader $streamReader
     * @return Fpdi\PdfParser\PdfParser|setasign\FpdiPdfParser\PdfParser\PdfParser
     */
    protected function getPdfParserInstance(Fpdi\PdfParser\StreamReader $streamReader)
    {
        if ($this->pdfParserClass !== null) {
            return new $this->pdfParserClass($streamReader);
        }

        return parent::getPdfParserInstance($streamReader);
    }

    /**
     * Checks whether a compressed cross-reference reader instance was used or not.
     *
     * @return bool
     */
    public function isCompressedXref()
    {
        foreach (array_keys($this->readers) as $readerId) {
            $crossReference = $this->getPdfReader($readerId)->getParser()->getCrossReference();
            $readers = $crossReference->getReaders();
            foreach ($readers as $reader) {
                if ($reader instanceof \setasign\FpdiPdfParser\PdfParser\CrossReference\CompressedReader) {
                    return true;
                }
            }
        }

        return false;
    }
}

$pdf = new Pdf();
if ($parser === 'default') {
    $pdf->setPdfParserClass(Fpdi\PdfParser\PdfParser::class);
}

$pdf->AddPage();
$pageCount = $pdf->setSourceFile($filename);
$tplIdx = $pdf->ImportPage(1);
$size = $pdf->useTemplate($tplIdx, 20, 20, 100);
$pdf->SetDrawColor(216);
$pdf->Rect(20, 20, 100, $size['height'], 'D');

$leftMargin = $pdf->getX() + 20 + 100;
$pdf->SetLeftMargin($leftMargin);
$pdf->SetXY($leftMargin, 20);
$pdf->SetFont('helvetica');

if ($pdf->isCompressedXref()) {
    $pdf->SetTextColor(72, 179, 84);
    $pdf->Write(5, 'This document uses new PDF compression technics introduced in PDF version 1.5 ;-)');
} else {
    $pdf->SetTextColor(182);
    $pdf->Write(5, 'This document should also work with the free parser version ;-)');
}

$pdf->Output('F', 'generated.pdf');