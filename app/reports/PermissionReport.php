<?php

namespace App\Reports;

use Codedge\Fpdf\Fpdf\Fpdf;

class PermissionReport extends Fpdf
{
    public function Header()
    {
        $this->SetFillColor(200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetFont('Arial','',12);

        $this->Cell(0,5, utf8_decode('Relatório de Permissões do Sistema'), 1, 1,'C', 1);
        $this->Ln(2);

        // cabeçalho do resultado
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetFont('Arial','B', 12);
        $this->Cell(80 ,6,'Nome', 0, 0,'L');
        $this->Cell(106 ,6, utf8_decode('Descrição'), 0, 0,'L');
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