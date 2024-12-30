<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\SimpleType\JcTable;

class ContractDownloadController extends Controller
{
    public function __invoke($id)
    {
        try {
            $contract = Contract::with(['tenant', 'apartment.building'])->findOrFail($id);

            $phpWord = new PhpWord();
            $phpWord->setDefaultFontName('Arial');
            $phpWord->setDefaultFontSize(12);

            $section = $phpWord->addSection();

            // Full-page border using a table
            $borderStyle = [
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 0,
            ];
            $phpWord->addTableStyle('FullPageBorder', $borderStyle);

            // Create a full-page table
            $table = $section->addTable('FullPageBorder');
            $table->addRow();
            $cell = $table->addCell(10000);

            // Add header table style for the inner table
            $headerTableStyle = [
                'borderSize' => 0,
                'cellMargin' => 50,
            ];

            // Create inner table for header content (2 columns)
            $headerTable = $cell->addTable('HeaderTable');
            $headerTable->addRow();
            
            // Left cell for company details (40% width)
            $leftCell = $headerTable->addCell(4000);
            $leftCell->addText("XAVIER BUSINESS CENTER", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);
            $leftCell->addText("27th Floor Al Saqar Tower,", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);
            $leftCell->addText("Sheikh Zayed Road, Dubai", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);
            $leftCell->addText("Trade Licence: 967319", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);
           
            $cell->addTextBreak(1);

            // Right cell for logo (60% width)
            $rightCell = $headerTable->addCell(6000, ['valign' => 'center']);
            $rightCell->addImage(
                public_path('images/xavier-logo.png'),
                [
                    'width' => 100,   
                    'height' => 70,   
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT 
                ]
            );
            
            // Add title
            $cell->addText("Tel: +971 50 172 5600, +971 50 924 5979, +971 50 924 5979 , Email: management@xavier.ae,", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);
            $cell->addText("Address: ibne battuta gate building near ibne battuta mall  metro station  ,", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);
            $cell->addText("OFFICE LEASE CONTRACT", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);

            // Add contract details table
            $contractTable = $cell->addTable('ContractTable');
            $this->addTableRowWithBottomBorder($contractTable, 'Company Name:', $contract->company, 'Landlord Name:', $contract->landlord_name);
            $this->addTableRowWithBottomBorder($contractTable, 'Land Location:', $contract->land_location, 'Location:', $contract->location);
            $this->addTableRowWithBottomBorder($contractTable, 'Tenant Name:', $contract->tenant->name, 'Trade License:', $contract->trade_license);
            $this->addTableRowWithBottomBorder($contractTable, 'Nationality:', $contract->nationality, 'EID No:', $contract->eid_no);
            $this->addTableRowWithBottomBorder($contractTable, 'Contract Start:', $contract->start_date, 'Contract End:', $contract->end_date);
            $this->addTableRowWithBottomBorder($contractTable, 'Ejari:', $contract->ejari, 'Contact No:', $contract->contact_no);
            // Add the new section with title and table
            $cell->addText("Building Details", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);
            // $phpWord->addTableStyle('ContractTable2', $tableStyle);
            $contractTable2 = $cell->addTable('contractTable2');
            $this->addTableRowWithBottomBorder($contractTable2, 'Address:', $contract->apartment->building->address, 'Building Number:', $contract->apartment->building->number);
            $this->addTableRowWithBottomBorder($contractTable2, 'Floor:', $contract->apartment->floor, 'Apartment:', $contract->apartment->number);
            // Add a new section with title and table for Additional Information
$cell->addText("Additional Information", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);



            // Add a new table for the signature section
            $signatureTable = $cell->addTable();

            // Add a row for signatures
            $signatureTable->addRow();

            // Left cell: Tenant signature
            $tenantCell = $signatureTable->addCell(5000);
            $tenantCell->addText("Tenant Signature / Stamp", ['bold' => true, 'size' => 10], ['alignment' => 'center']);
            $tenantCell->addText("-----------------------", [], ['alignment' => 'center',]);

            // Right cell: Landlord signature
            $landlordCell = $signatureTable->addCell(5000);
            $landlordCell->addText("Landlord Signature / Stamp", ['bold' => true, 'size' => 10], ['alignment' => 'center']);
            $landlordCell->addText("-----------------------", [], ['alignment' => 'center',]);

            // Set filename and save
            $fileName = 'Contract_' . $contract->id . '_' . time() . '.docx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save('php://output');
            exit();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Modified addTableRow method to accept four parameters for label and value pairs
    private function addTableRow($table, $label1, $value1, $label2 = null, $value2 = null)
    {
        $table->addRow();
        $table->addCell(2500)->addText($label1, ['bold' => true]);
        $table->addCell(2500)->addText($value1);
        
        // Add second label and value pair only if provided
        if ($label2 && $value2) {
            $table->addCell(2500)->addText($label2, ['bold' => true]);
            $table->addCell(2500)->addText($value2);
        }
    }
    private function addTableRowWithBottomBorder($table, $label1, $value1, $label2 = null, $value2 = null)
{
    $table->addRow();

    // Define cell style with a bottom border
    $cellStyle = ['borderBottomSize' => 6, 'borderBottomColor' => '000000' ,'height'=> 300];
    
    $labelStyle = ['bold' => true, 'size' => 10];  // Set your desired font size for label
    $valueStyle = ['size' => 9 ,'bold'=> true];
    // Add the first label and value cells
    $cell1 = $table->addCell(2500, $cellStyle);
    $cell1->addText($label1,$labelStyle);
    
    $cell2 = $table->addCell(2500, $cellStyle);
    $cell2->addText($value1,$valueStyle);

    // Add second label and value pair if provided
    if ($label2 && $value2) {
        $cell3 = $table->addCell(2500, $cellStyle);
        $cell3->addText($label2, $labelStyle);
        
        $cell4 = $table->addCell(2500, $cellStyle);
        $cell4->addText($value2 ,$valueStyle);
    }
}
}
