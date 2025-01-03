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
            $phpWord->setDefaultFontSize(9); // Slightly reduced from 12 to fit better

            // Configure A4 page size and margins
            $section = $phpWord->addSection([
                'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(21),  // A4 width
                'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(29.7), // A4 height
                'marginLeft' => 100,
                'marginRight' => 100,
                'marginTop' => 100,
                'marginBottom' => 100,
            ]);

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

            // Header table style
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
           
            // Reduced break
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
            
            // Contact details
            $cell->addText("Tel: +971 50 172 5600, +971 50 924 5979, +971 50 924 5979 , Email: management@xavier.ae,", ['bold' => true, 'size' => 10, 'color' => '#4465A1'], ['alignment' => 'left', 'spacing' => 0]);
            $cell->addText("Address: ibne battuta gate building near ibne battuta mall  metro station  ,", ['bold' => true, 'size' => 10, 'color' => '#4465A1'], ['alignment' => 'left', 'spacing' => 0]);
            $cell->addText("OFFICE LEASE CONTRACT", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);

            // Contract details table with reduced height
            $contractTable = $cell->addTable('ContractTable');
            $this->addTableRowWithBottomBorder($contractTable, 'Company Name:', $contract->company, 'Landlord Name:', $contract->landlord_name);
            $this->addTableRowWithBottomBorder($contractTable, 'Land Location:', $contract->land_location, 'Location:', $contract->location);
            $this->addTableRowWithBottomBorder($contractTable, 'Tenant Name:', $contract->tenant->name, 'Trade License:', $contract->trade_license);
            $this->addTableRowWithBottomBorder($contractTable, 'Nationality:', $contract->nationality, 'EID No:', $contract->eid_no);
            $this->addTableRowWithBottomBorder($contractTable, 'Contract Start:', $contract->start_date, 'Contract End:', $contract->end_date);
            $this->addTableRowWithBottomBorder(
                $contractTable, 
                'Ejari:', 
                $contract->ejari >= 0 ? 'Yes' : 'No', 
                'Contact No:', 
                $contract->contact_no
            );
            
            // Payment Details section
            $cell->addText("Payment Details", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);
            $paymentTable = $cell->addTable('PaymentTable');
            $this->addTableRowWithBottomBorder($paymentTable, 'Date', 'Payee Bank','Amount', 'Narration');

            foreach ($contract->tenant->dues->filter(fn($due) => $due->status) as $paidDue) {
                $this->addTableRowWithBottomBorder(
                    $paymentTable,
                    formatDate($paidDue->created_at),
                    $paidDue->payment_method,
                    formatCurrency($paidDue->paid_amount),
                    $paidDue->note ?: '-'
                );
            }

            // Package Details section
            $cell->addText("Package Details", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);
            $packageTable = $cell->addTable('PackageTable');
            
            $building = $contract->apartment->building;
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

            // Package details rows
            $this->addTableRowWithBottomBorder($packageTable, 'Executive Table:', $building->executive_table ?? '-', 'Executive Chair:', $building->executive_chair ?? '-');
            $this->addTableRowWithBottomBorder($packageTable, 'Guest Chair:', $building->guest_chair ?? '-', 'Staff Workstations:', $building->staff_workstations ?? '-');
            $this->addTableRowWithBottomBorder($packageTable, 'Staff Chairs:', $building->staff_chairs ?? '-', 'Cabinet:', $building->cabinet ?? '-');
            $this->addTableRowWithBottomBorder($packageTable, 'Conference Room:', $building->conference_room ?? '-', 'Sofa:', $building->sofa ?? '-');
            $this->addTableRowWithBottomBorder($packageTable, 'Cleaning:', $building->cleaning ?? '-', 'Parking:', $building->parking ?? '-');
            $this->addTableRowWithBottomBorder($packageTable, 'Drinking Water:', $building->drinking_water ?? '-', 'Electricity:', $building->electricity ?? '-');
            $this->addTableRowWithBottomBorder($packageTable, 'Internet:', $building->internet ?? '-', 'Conference Room:', $building->conference_room ?? '-');

            // Account Details section
           // Add the "Account Details" section title
                // Add the "Account Details" section title
                $cell->addText("Account Details", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'shading' => ['fill' => '1F3864']]);

                // Add the table for account details
                $accountDetailsTable = $cell->addTable('AccountDetailsTable');

                // Define column headings
                $accountDetailsTable->addRow();
                $accountDetailsTable->addCell(3000, ['borderBottomSize' => 6, 'borderBottomColor' => '000000'])
                    ->addText('Particulars', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);
                $accountDetailsTable->addCell(2000, ['borderBottomSize' => 6, 'borderBottomColor' => '000000'])
                    ->addText('Rent Amount', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);
                $accountDetailsTable->addCell(2000, ['borderBottomSize' => 6, 'borderBottomColor' => '000000'])
                    ->addText('Discount / Wave', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);
                $accountDetailsTable->addCell(2000, ['borderBottomSize' => 6, 'borderBottomColor' => '000000'])
                    ->addText('Net Amount', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);

                // Add rows for each account detail
                $actualOfficeRent = $contract->actual_office_rent ?? 0;
                $discount = $contract->discount ?? 0;

                // Calculate Net Amount
                $netRent = $actualOfficeRent - $discount;

                $accountDetails = [
                    ['Actual Office Rent', $actualOfficeRent, $discount, $netRent],
                    ['Admin Fee', '-', '-', $contract->admin_fee ?? '-'],
                    ['Security Deposit', '-', '-', $contract->security_deposit ?? '-'],
                    ['VAT 5%', '-', '-', $contract->vat ?? '-'],
                    ['Parking Card Fee', '-', '-', $contract->parking_card_fee ?? '-'],
                    ['Commission', '-', '-', $contract->commission ?? '-'],
                    ['Ejari', '-', '-', $contract->ejari ?? '-'],
                ];

                foreach ($accountDetails as $row) {
                    $accountDetailsTable->addRow();
                    foreach ($row as $key => $value) {
                        $alignment = $key === 0 ? ['alignment' => 'left'] : ['alignment' => 'center'];
                        $accountDetailsTable->addCell(2000, ['borderBottomSize' => 6, 'borderBottomColor' => '000000'])
                            ->addText($value, ['size' => 11], $alignment);
                    }
                }



            // Signature section
            $signatureTable = $cell->addTable();
            $signatureTable->addRow();
            
            $tenantCell = $signatureTable->addCell(5000);
            $tenantCell->addText("Tenant Signature / Stamp", ['bold' => true, 'size' => 10], ['alignment' => 'center']);
            $tenantCell->addText("-----------------------", [], ['alignment' => 'center']);

            $landlordCell = $signatureTable->addCell(5000);
            $landlordCell->addText("Landlord Signature / Stamp", ['bold' => true, 'size' => 10], ['alignment' => 'center']);
            $landlordCell->addText("-----------------------", [], ['alignment' => 'center']);

            // Save document
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

    private function addTableRowWithBottomBorder($table, ...$params)
    {
        $table->addRow(250); // Reduced row height for better fit

        $cellStyle = [
            'borderBottomSize' => 6,
            'borderBottomColor' => '000000',
            'valign' => 'center',
            'spaceAfter' => 0,
            'spaceBefore' => 0,
            'spacing' => 0,
        ];

        $cellStyleFullBorder = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'valign' => 'center',
            'spaceAfter' => 0,
            'spaceBefore' => 0,
            'spacing' => 0,
        ];

        $textStyle = [
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
        ];

        // Check if this is a payment table row (4 columns) or a regular row (2 label-value pairs)
        $isPaymentTable = $params[0] === 'Date' || strtotime($params[0]) !== false;

        if ($isPaymentTable) {
            $columnWidths = [2000, 2000, 3000, 3000];
            
            for ($i = 0; $i < count($params); $i++) {
                $cell = $table->addCell($columnWidths[$i], $cellStyleFullBorder);
                $cell->addText($params[$i] ?? '-', ['size' => 9, 'bold' => true], $textStyle);
            }
        } else {
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