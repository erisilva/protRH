<?php

namespace App\Reports;

use Codedge\Fpdf\Fpdf\Fpdf;

class FuncionarioReport extends Fpdf
{
    public function Header()
    {
        $this->SetFillColor(200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetFont('Arial','',12);

        $this->Cell(186, 6, utf8_decode('Relatório de Funcionários'), 1, 1,'C', 1);
        $this->Ln(2);

        // cabeçalho do resultado
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetFont('Arial','B', 9);
        $this->Cell(76 ,6,'Nome', 0, 0,'L');
        $this->Cell(30 ,6, utf8_decode('Matrícula'), 0, 0,'L');
        $this->Cell(65 ,6, utf8_decode('E-mail'), 0, 0,'L');
        $this->Cell(15 ,6, utf8_decode('Nº Pasta'), 0, 0,'R');
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