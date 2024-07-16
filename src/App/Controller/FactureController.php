<?php

namespace App\Controller;

use App\Model\FactureModel;
require('path/to/fpdf.php');

use FPDF;
class FactureController
{
    private $factureModel;

    public function __construct($factureModel)
    {
        $this->factureModel = $factureModel;
    }

    public function creerFacture($detteId, $clientId, $montantTotal)
    {
        $factureId = $this->factureModel->creerFacture($detteId, $clientId, $montantTotal);
        return $this->factureModel->obtenirFactureParId($factureId);
    }

    public function voirFacture($id)
    {
        return $this->factureModel->obtenirFactureParId($id);
    }

    public function genererPDF($facture)
    {
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Facture', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Facture ID: ' . $facture['id'], 0, 1);
        $pdf->Cell(0, 10, 'Client ID: ' . $facture['client_id'], 0, 1);
        $pdf->Cell(0, 10, 'Montant Total: ' . $facture['montant_total'] . ' EUR', 0, 1);
        $pdf->Cell(0, 10, 'Date de la facture: ' . $facture['date_facture'], 0, 1);
        $pdf->Output('I', 'facture.pdf');
    }
}
