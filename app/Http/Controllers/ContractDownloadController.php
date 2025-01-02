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
            $cell->addText("Tel: +971 50 172 5600, +971 50 924 5979, +971 50 924 5979 , Email: management@xavier.ae,", ['bold' => true, 'size' => 10, 'color' => '#4465A1'], ['alignment' => 'left']);
            $cell->addText("Address: ibne battuta gate building near ibne battuta mall  metro station  ,", ['bold' => true, 'size' => 10, 'color' => '#4465A1'], ['alignment' => 'left']);
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

            
           // Add a new section with title and table for Paid Dues
            $cell->addText("Payment Details", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);

            // Create a new table for Paid Dues with the same style
            $paymentTable = $cell->addTable('PaymentTable');


            $paymentTable = $cell->addTable('PaymentTable');

            // Add header row
            $this->addTableRowWithBottomBorder($paymentTable, 'Date', 'Payee Bank','Amount', 'Narration', );

            // Populate the table with Paid Dues
            foreach ($contract->tenant->dues->filter(fn($due) => $due->status) as $paidDue) {
                $this->addTableRowWithBottomBorder(
                    $paymentTable,
                    formatDate($paidDue->created_at),
                    $paidDue->payment_method,
                    formatCurrency($paidDue->paid_amount),
                    $paidDue->note ?: '-',
                );
            }

          // Add a new section with title and table for Package Details
                    $cell->addText("Package Details", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);

                    // Create a new table for Package Details with the same style
                    $packageTable = $cell->addTable('PackageTable');

                    // Populate the table with package details from the building
                    $building = $contract->apartment->building;

                    // Check if the building exists to avoid null errors
                    if (!$building) {
                        $building = (object)[
                            'executive_table' => '-',
                            'executive_chair' => '-',
                            'guest_chair' => '-',
                            'staff_workstations' => '-',
                            'staff_chairs' => '-',
                            'cabinet' => '-',
                            'conference_room' => '-',
                            'sofa' => '-',
                            'cleaning' => '-',
                            'parking' => '-',
                            'drinking_water' => '-',
                            'electricity' => '-',
                            'internet' => '-',
                            'refreshment_tea_coffee' => '-',
                            
                        ];
                    }

                    // Add rows for package details
                    $this->addTableRowWithBottomBorder(
                        $packageTable,
                        'Executive Table:', $building->executive_table ?? '-',
                        
                        'Executive Chair:', $building->executive_chair ?? '-'
                    );

                    $this->addTableRowWithBottomBorder(
                        $packageTable,
                        'Guest Chair:', $building->guest_chair ?? '-',
                        'Staff Workstations:', $building->staff_workstations ?? '-'
                    );

                    $this->addTableRowWithBottomBorder(
                        $packageTable,
                        'Staff Chairs:', $building->staff_chairs ?? '-',
                        'Cabinet:', $building->cabinet ?? '-'
                    );
                    $this->addTableRowWithBottomBorder(
                        $packageTable,
                        'conference_room:', $building->conference_room ?? '-',
                        'sofa:', $building->sofa ?? '-'
                    );
                     $this->addTableRowWithBottomBorder(
                        $packageTable,
                        'cleaning:', $building->cleaning ?? '-',
                        'parking:', $building->parking ?? '-'
                    );
                    $this->addTableRowWithBottomBorder(
                        $packageTable,
                        'drinking_water:', $building->drinking_water ?? '-',
                        'electricity:', $building->electricity ?? '-'
                    ); 
                    $this->addTableRowWithBottomBorder(
                        $packageTable,
                        'internet:', $building->internet ?? '-',
                        'conference_room:', $building->conference_room ?? '-'
                    );
                  // Add a new section with title and table for Account Details
$cell->addText("Account Details", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);

// Create a new table for Account Details with the same style
$packageTable = $cell->addTable('PackageTable');

// Filter dues for the specific contract
$dues = $contract->tenant->dues->filter(fn($due) => $due->status && $due->contract_id == $contract->id);

// Check if there are dues available
if ($dues->isNotEmpty()) {
    foreach ($dues as $due) {
        // Add rows for dues details
        $this->addTableRowWithBottomBorder(
            $packageTable,
            'Due Description:', $due->description ?? '-', // Use 'description' column
            'Amount:', $due->amount ?? '-'               // Use 'amount' column
        );

        $this->addTableRowWithBottomBorder(
            $packageTable,
            'Admin Fee:', $due->admin_fee ?? '-',        // Use 'admin_fee' column
            'VAT:', $due->vat ?? '-'                    // Use 'vat' column
        );

        $this->addTableRowWithBottomBorder(
            $packageTable,
            'Security Deposit:', $due->security_deposit ?? '-', // Use 'security_deposit' column
            'Commission:', $due->commission ?? '-'             // Use 'commission' column
        );

        $this->addTableRowWithBottomBorder(
            $packageTable,
            'Parking Card Fee:', $due->parking_card_fee ?? '-', // Use 'parking_card_fee' column
            'Ejari:', $due->ejari ?? '-'                       // Use 'Ejari' column
        );
    }
} else {
    // Add a single row if no dues are available
    $this->addTableRowWithBottomBorder(
        $packageTable,
        'Dues:', 'No dues available',
        '', ''
    );
}


                    


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


private function addTableRowWithBottomBorder($table, ...$params)
{
    $table->addRow();

    // Define styles for vertical and horizontal centering
    $cellStyle = [
        'borderBottomSize' => 6,
        'borderBottomColor' => '000000',
        'height' => 300,
        'valign' => 'center', // Vertical centering
    ];

    $cellStyleFullBorder = [
        'borderSize' => 6,
        'borderColor' => '000000',
        'height' => 300,
        'valign' => 'center', // Vertical centering
    ];

    $textStyle = [
        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, // Horizontal centering
    ];

    // Check if this is a payment table row (4 columns) or a regular row (2 label-value pairs)
    $isPaymentTable = $params[0] === 'Date' || strtotime($params[0]) !== false;

    if ($isPaymentTable) {
        $columnWidths = [2000, 2000, 3000, 3000]; // Adjusted widths for 4 columns
        
        for ($i = 0; $i < count($params); $i++) {
            $cell = $table->addCell($columnWidths[$i], $cellStyleFullBorder);
            $cell->addText($params[$i] ?? '-', ['size' => 9, 'bold' => true], $textStyle);
        }
    } else {
        // Handle regular label-value pairs (original format)
        $cell1 = $table->addCell(2500, $cellStyle);
        $cell1->addText($params[0], ['bold' => true], $textStyle);
        
        $cell2 = $table->addCell(2500, $cellStyle);
        $cell2->addText($params[1], ['size' => 9, 'bold' => true], $textStyle);

        if (isset($params[2]) && isset($params[3])) {
            $cell3 = $table->addCell(2500, $cellStyle);
            $cell3->addText($params[2], ['bold' => true], $textStyle);
            
            $cell4 = $table->addCell(2500, $cellStyle);
            $cell4->addText($params[3], ['size' => 9, 'bold' => true], $textStyle);
        }
    }
}

}
