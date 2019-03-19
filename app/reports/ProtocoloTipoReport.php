<?php

namespace App\Reports;

use Codedge\Fpdf\Fpdf\Fpdf;

class ProtocoloTipoReport extends Fpdf
{
    public function Header()
    {
        $this->SetFillColor(200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetFont('Arial','',12);

        $this->Cell(186, 6, utf8_decode('Relatório de Tipos de Protocolo'), 1, 1,'C', 1);
        $this->Ln(2);

        // cabeçalho do resultado
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetFont('Arial','B', 11);
        $this->Cell(186 ,6, utf8_decode('Descrição'), 0, 0,'L');
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 11);
        $this->Cell(0, 1, '', 'T', 1, 'L');
        $this->Cell(93, 5, date('d/m/Y H:i:s'), 0, 0, 'L');
        $this->Cell(93, 5, utf8_decode('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
    }
}