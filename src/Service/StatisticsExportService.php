<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twig\Environment;

class StatisticsExportService
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generatePDF(array $data): Response
    {
        // Configure Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($pdfOptions);

        // Render the HTML template
        $html = $this->twig->render('statistics/statistics_pdf.html.twig', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Create response
        $response = new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="statistiques_' . date('Y-m-d') . '.pdf"'
            ]
        );

        return $response;
    }

    public function generateExcel(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Système de Maintenance IT')
            ->setTitle('Statistiques - ' . date('Y-m-d'))
            ->setSubject('Rapport de statistiques')
            ->setDescription('Statistiques du système de maintenance IT');

        // Title
        $sheet->setCellValue('A1', 'RAPPORT DE STATISTIQUES');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Généré le: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $currentRow = 4;

        // Summary statistics
        $sheet->setCellValue('A' . $currentRow, 'RÉSUMÉ GÉNÉRAL');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
        $currentRow += 2;

        $summaryData = [
            ['Indicateur', 'Valeur'],
            ['Total Équipements', $data['totalEquipments']],
            ['Total Consommables', $data['totalConsommables']],
            ['Total Interventions', $data['totalInterventions']],
            ['Valeur Consommables', number_format($data['totalConsommableValue'], 0, ',', ' ') . ' Dt']
        ];

        foreach ($summaryData as $row) {
            $sheet->setCellValue('A' . $currentRow, $row[0]);
            $sheet->setCellValue('B' . $currentRow, $row[1]);
            $currentRow++;
        }

        // Style summary table
        $sheet->getStyle('A' . ($currentRow - 5) . ':B' . ($currentRow - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
        
        $sheet->getStyle('A' . ($currentRow - 5) . ':B' . ($currentRow - 5))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($currentRow - 5) . ':B' . ($currentRow - 5))->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E2E2');

        $currentRow += 2;

        // Equipment by state
        if (!empty($data['equipmentsByState'])) {
            $sheet->setCellValue('A' . $currentRow, 'ÉQUIPEMENTS PAR ÉTAT');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
            $currentRow += 2;

            $sheet->setCellValue('A' . $currentRow, 'État');
            $sheet->setCellValue('B' . $currentRow, 'Nombre');
            $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

            foreach ($data['equipmentsByState'] as $equipment) {
                $sheet->setCellValue('A' . $currentRow, $equipment['Etat']);
                $sheet->setCellValue('B' . $currentRow, $equipment['count']);
                $currentRow++;
            }
            $currentRow++;
        }

        // Top equipment models
        if (!empty($data['equipmentsByModel'])) {
            $sheet->setCellValue('A' . $currentRow, 'TOP 10 MODÈLES D\'ÉQUIPEMENTS');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
            $currentRow += 2;

            $sheet->setCellValue('A' . $currentRow, 'Modèle');
            $sheet->setCellValue('B' . $currentRow, 'Quantité');
            $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

            foreach ($data['equipmentsByModel'] as $model) {
                $sheet->setCellValue('A' . $currentRow, $model['Modele'] ?? 'Non spécifié');
                $sheet->setCellValue('B' . $currentRow, $model['count']);
                $currentRow++;
            }
            $currentRow++;
        }

        // Low stock alerts
        if (!empty($data['lowStockConsommables'])) {
            $sheet->setCellValue('A' . $currentRow, 'ALERTES STOCK FAIBLE');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $currentRow)->getFont()->getColor()->setARGB('FFFF0000');
            $currentRow += 2;

            $sheet->setCellValue('A' . $currentRow, 'Désignation');
            $sheet->setCellValue('B' . $currentRow, 'Modèle');
            $sheet->setCellValue('C' . $currentRow, 'Stock');
            $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

        foreach ($data['lowStockConsommables'] as $consommable) {
            // Use object methods instead of array access
            $sheet->setCellValue('A' . $currentRow, $consommable->getDesignation() ?? 'N/A');
            $sheet->setCellValue('B' . $currentRow, $consommable->getModele() ?? 'N/A');
            $sheet->setCellValue('C' . $currentRow, $consommable->getQuantite());
            $sheet->setCellValue('D' . $currentRow, $consommable->getValeur() ?? 0);
            $sheet->setCellValue('E' . $currentRow, $consommable->getEtat() ?? 'N/A');
            $currentRow++;
        }
        $currentRow++;
        }

        // Recent interventions
        if (!empty($data['recentInterventions'])) {
            $sheet->setCellValue('A' . $currentRow, 'INTERVENTIONS RÉCENTES');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
            $currentRow += 2;

            $sheet->setCellValue('A' . $currentRow, 'Date');
            $sheet->setCellValue('B' . $currentRow, 'Type');
            $sheet->setCellValue('C' . $currentRow, 'Équipement');
            $sheet->setCellValue('D' . $currentRow, 'Description');
            $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

            foreach ($data['recentInterventions'] as $intervention) {
                // Use object methods instead of array access
                $sheet->setCellValue('A' . $currentRow, $intervention->getDateIntervention()->format('d/m/Y'));
                $sheet->setCellValue('B' . $currentRow, ucfirst($intervention->getTypeIntervention()));
                
                // Handle equipment relationship
                $equipement = $intervention->getEquipement();
                $equipementModel = $equipement ? $equipement->getModele() : 'N/A';
                $sheet->setCellValue('C' . $currentRow, $equipementModel);
                
                $sheet->setCellValue('D' . $currentRow, $intervention->getDescription());
                $currentRow++;
            }
        }
        // Auto-size columns
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Create Excel writer
        $writer = new Xlsx($spreadsheet);

        // Create streamed response
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="statistiques_' . date('Y-m-d') . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}