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
            $phpWord->setDefaultFontSize(9);

            // Configure A4 page size with minimal margins for full width
            $section = $phpWord->addSection([
                'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(21),
                'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(29.7),
                'marginLeft' => 200,
                'marginRight' => 50,
                'marginTop' => 100,
                'marginBottom' => 100,
            ]);

            // Full-page border using a table with full width
            $borderStyle = [
                'borderSize' => 10,
                'borderColor' => '000000',
                'cellMargin' => 0,
                'width' => 100,
                'unit' => 'pct',
            ];
            $phpWord->addTableStyle('FullPageBorder', $borderStyle);

            // Create a full-page table
            $table = $section->addTable('FullPageBorder');
            $table->addRow();
            $cell = $table->addCell(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(20));

            // Header table style
            // $headerTableStyle = [
            //     'borderSize' => 0,
            //     'cellMargin' => 50,
            //     'width' => 100,
            //     'unit' => 'pct',
            // ];
            $headerTableStyle = [

                'cellMarginLeft' => 200,  // Padding left
                'cellMarginRight' => 200, // Padding right
                'cellMarginTop' => 100,   // Padding top
                'cellMarginBottom' => 100, // Padding bottom
                'width' => 100,
                'unit' => 'pct',
            ];
            $phpWord->addTableStyle('HeaderTable', $headerTableStyle);

            // Create inner table for header content
            $headerTable = $cell->addTable('HeaderTable');
            $headerTable->addRow();

            // Left cell for company details
            $leftCell = $headerTable->addCell(4000);
            $leftCell->addText("");
            $leftCell->addText("");

            $leftCell->addText("XAVIER BUSINESS CENTER", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);
            $leftCell->addText("27th Floor Al Saqar Tower,", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);
            $leftCell->addText("Sheikh Zayed Road, Dubai", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);
            $leftCell->addText("Trade Licence: 967319", ['bold' => true, 'size' => 10, 'color' => '#000'], ['alignment' => 'left']);

            // $cell->addTextBreak(1);

            // Right cell for logo
            $rightCell = $headerTable->addCell(6000, ['valign' => 'center']);
            $rightCell->addImage(
                public_path('images/xavier-logo.png'),
                [
                    'width' => 100,
                    'height' => 70,
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT
                ]
            );

            // Define table style for contact details
$contactTableStyle = [

    'cellMarginLeft' => 200,  // Padding left
    'cellMarginRight' => 20, // Padding right
    'cellMarginTop' => 100,   // Padding top
    'cellMarginBottom' => 100, // Padding bottom
    'width' => 100,
    'unit' => 'pct',
];

// Apply table style for contact details
$phpWord->addTableStyle('ContactTable', $contactTableStyle);

// Create table for contact details
$contactTable = $cell->addTable('ContactTable');
$contactTable->addRow();

// Add contact details in a single cell
$contactCell = $contactTable->addCell(11500, ['valign' => 'top']);
$contactCell->addText(
    "Tel: +971 50 172 5600, +971 50 924 5979, +971 50 924 5979, Email: management@xavier.ae ,ibne battuta gate building near ibne battuta mall metro station",
    [ 'size' => 8, 'color' => '#4465A1'],
    ['alignment' => 'left', 'spacing' => 0]
);
// $contactCell->addText(
//     "Address: ibne battuta gate building near ibne battuta mall metro station",
//     ['bold' => true, 'size' => 10, 'color' => '#4465A1'],
//     ['alignment' => 'left', 'spacing' => 0]
// );

            $cell->addText("OFFICE LEASE CONTRACT", ['bold' => true, 'size' => 14,'underline' => 'single'], ['alignment' => 'center',  'spaceBefore' => 0,
    'spaceAfter' => 0,'shading' => ['fill' => '1F3864']]);

            // Contract details table
            $contractTableStyle = [
                'width' => 100,
                'unit' => 'pct',
                'cellMargin' => 0,
            ];
            $phpWord->addTableStyle('ContractTable', $contractTableStyle);

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
// Account Details section
$cell->addText("Account Details", ['bold' => true, 'size' => 12], ['alignment' => 'center',  'spaceBefore' => 0,
'spaceAfter' => 0,'shading' => ['fill' => '1F3864']]);
        $accountDetailsTable = $cell->addTable('ContractTable');


        // Define Account Details array before the loop
$actualOfficeRent = $contract->actual_office_rent ?? 0;
$discount = $contract->discount ?? 0;
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

// Define column headings
$accountDetailsTable->addRow();

$accountDetailsTable->addCell(3200, [
'borderBottomSize' => 6,
'borderBottomColor' => '000000',
'borderRightSize' => 6,
'borderRightColor' => '000000'
])->addText('Particulars', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);

$accountDetailsTable->addCell(3200, [
'borderBottomSize' => 6,
'borderBottomColor' => '000000',
'borderRightSize' => 6,
'borderRightColor' => '000000'
])->addText('Rent Amount', ['bold' => true, 'color' => '000000', 'size' => 12],  ['alignment' => 'center']);

$accountDetailsTable->addCell(3000, [
'borderBottomSize' => 6,
'borderBottomColor' => '000000',
'borderRightSize' => 6,
'borderRightColor' => '000000'
])->addText('Discount / Wave', ['bold' => true, 'color' => '000000', 'size' => 12],  ['alignment' => 'center']);

$accountDetailsTable->addCell(2500, [
'borderBottomSize' => 6,
'borderBottomColor' => '000000',
'borderRightSize' => 6,
'borderRightColor' => '000000'
// ])->addText('Net Amount', ['bold' => true, 'color' => '000000', 'size' => 12], ['indentation' => ['left' => 200]]);
])->addText('Net Amount', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);

// Add data rows with right borders and left padding
foreach ($accountDetails as $row) {
$accountDetailsTable->addRow();
foreach ($row as $key => $value) {
    $alignment = $key === 0 ? ['alignment' => 'left'] : ['alignment' => 'left'];
    $accountDetailsTable->addCell(2000, [
        'borderBottomSize' => 6,
        'borderBottomColor' => '000000',
        'borderRightSize' => 6,
        'borderRightColor' => '000000'
    ])->addText($value, ['size' => 9, 'bold' => true], ['indentation' => ['left' => 100]]);
}
}
            // Payment Details section
            // $cell->addText("Payment Details", ['bold' => true, 'size' => 14], ['alignment' => 'center', 'spaceBefore' => 0,
            // 'spaceAfter' => 0, 'shading' => ['fill' => '1F3864']]);
            // $paymentTable = $cell->addTable('ContractTable');
            // $this->addTableRowWithBottomBorder($paymentTable, 'Date', 'Payee Bank','Amount', 'Narration');

            // foreach ($contract->tenant->dues->filter(fn($due) => $due->status) as $paidDue) {
            //     $this->addTableRowWithBottomBorder(
            //         $paymentTable,
            //         formatDate($paidDue->created_at),
            //         $paidDue->payment_method,
            //         formatCurrency($paidDue->paid_amount),
            //         $paidDue->note ?: '-'
            //     );
            // }
            // Payment Details section
$cell->addText("Payment Details", ['bold' => true, 'size' => 12], [
    'alignment' => 'center',
    'spaceBefore' => 0,
    'spaceAfter' => 0,
    'shading' => ['fill' => '1F3864']
]);

$paymentTable = $cell->addTable('ContractTable');

// Define column headings with center alignment
$paymentTable->addRow();

$paymentTable->addCell(3200, [
    'borderBottomSize' => 6,
    'borderBottomColor' => '000000',
    'borderRightSize' => 6,
    'borderRightColor' => '000000'
])->addText('Date', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);

$paymentTable->addCell(3200, [
    'borderBottomSize' => 6,
    'borderBottomColor' => '000000',
    'borderRightSize' => 6,
    'borderRightColor' => '000000'
])->addText('Payee Bank', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);

$paymentTable->addCell(3000, [
    'borderBottomSize' => 6,
    'borderBottomColor' => '000000',
    'borderRightSize' => 6,
    'borderRightColor' => '000000'
])->addText('Amount', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);

$paymentTable->addCell(2500, [
    'borderBottomSize' => 6,
    'borderBottomColor' => '000000',
    'borderRightSize' => 6,
    'borderRightColor' => '000000'
])->addText('Narration', ['bold' => true, 'color' => '000000', 'size' => 12], ['alignment' => 'center']);

// Add data rows with borders and indentation (same layout styling)
foreach ($contract->tenant->dues->filter(fn($due) => $due->status) as $paidDue) {
    $paymentTable->addRow();

    $paymentTable->addCell(3000, [
        'borderBottomSize' => 6,
        'borderBottomColor' => '000000',
        'borderRightSize' => 6,
        'borderRightColor' => '000000'
    ])->addText(formatDate($paidDue->created_at), ['size' => 9, 'bold' => true], ['alignment' => 'center']);

        // Payee Bank - Show value if available, otherwise '-'
        $paymentTable->addCell(3000, [
            'borderBottomSize' => 6,
            'borderBottomColor' => '000000',
            'borderRightSize' => 6,
            'borderRightColor' => '000000'
        ])->addText(ucfirst($paidDue->payment_method ?: '-'), ['size' => 9, 'bold' => true], ['alignment' => 'center']);


    $paymentTable->addCell(3000, [
        'borderBottomSize' => 6,
        'borderBottomColor' => '000000',
        'borderRightSize' => 6,
        'borderRightColor' => '000000'
    ])->addText(formatCurrency($paidDue->paid_amount), ['size' => 9, 'bold' => true], ['alignment' => 'center']);

    $paymentTable->addCell(2500, [
        'borderBottomSize' => 6,
        'borderBottomColor' => '000000',
        'borderRightSize' => 6,
        'borderRightColor' => '000000'
    ])->addText($paidDue->note ?: '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);
}


            // Package Details section
            $cell->addText("Package Details", ['bold' => true, 'size' => 12], ['alignment' => 'center', 'spaceBefore' => 0,
            'spaceAfter' => 0, 'shading' => ['fill' => '1F3864']]);

            $packageTable = $cell->addTable('ContractTable');

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

            // First Row
            $packageTable->addRow();
            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Executive Table:', ['size' => 9, 'bold' => true,'indentation' => ['left' => 100] // Add left indentation
        ], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->executive_table ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Executive Chair:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->executive_chair ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            // Second Row
            $packageTable->addRow();
            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Guest Chair:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->guest_chair ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Staff Workstations:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->staff_workstations ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            // Third Row
            $packageTable->addRow();
            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Staff Chairs:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->staff_chairs ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Cabinet:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->cabinet ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            // Fourth Row
            $packageTable->addRow();
            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Conference Room:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->conference_room ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Sofa:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->sofa ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            // Fifth Row
            $packageTable->addRow();
            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Cleaning:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->cleaning ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Parking:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->parking ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            // Sixth Row
            $packageTable->addRow();
            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Drinking Water:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->drinking_water ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Electricity:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->electricity ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            // Seventh Row
            $packageTable->addRow();
            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Internet:', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->internet ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText('Refreshment (Tea/Coffee):', ['size' => 9, 'bold' => true], ['alignment' => 'left']);

            $packageTable->addCell(3000, [
                'borderBottomSize' => 6,
                'borderBottomColor' => '000000',
                'borderRightSize' => 6,
                'borderRightColor' => '000000'
            ])->addText($building->refreshment_tea_coffee ?? '-', ['size' => 9, 'bold' => true], ['alignment' => 'center']);





            // Signature section with full width
            $signatureTableStyle = [
                'width' => 100,
                'unit' => 'pct',
                'cellMargin' => 0,
            ];
            $phpWord->addTableStyle('SignatureTable', $signatureTableStyle);

            $signatureTable = $cell->addTable('SignatureTable');
            $signatureTable->addRow();

            $tenantCell = $signatureTable->addCell(null, ['width' => 50, 'unit' => 'pct']);
            $tenantCell->addText("Tenant Signature / Stamp", ['bold' => true, 'size' => 10], ['alignment' => 'center']);
            $tenantCell->addText("-----------------------", [], ['alignment' => 'center']);

            $landlordCell = $signatureTable->addCell(null, ['width' => 50, 'unit' => 'pct']);
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

    // private function addTableRowWithBottomBorder($table, ...$params)
    // {
    //     $table->addRow(250);

    //     $cellStyle = [
    //         'borderBottomSize' => 6,
    //         'borderBottomColor' => '000000',
    //         'valign' => 'center',
    //         'spaceAfter' => 0,
    //         'spaceBefore' => 0,
    //         'spacing' => 0,
    //         'width' => 25,
    //         'unit' => 'pct',
    //     ];

    //     $textStyle = [
    //         'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
    //     ];

    //     $isPaymentTable = $params[0] === 'Date' || strtotime($params[0]) !== false;

    //     if ($isPaymentTable) {
    //         // All cells get equal width (25%) for payment table
    //         foreach ($params as $value) {
    //             $cell = $table->addCell(null, $cellStyle);
    //             $cell->addText($value ?? '-', ['size' => 9, 'bold' => true], $textStyle);
    //         }
    //     } else {
    //         // Two pairs of label-value cells, each pair gets 50% width
    //         foreach (array_chunk($params, 2) as $pair) {
    //             $labelCell = $table->addCell(null, $cellStyle);
    //             $labelCell->addText($pair[0], ['bold' => true], $textStyle);

    //             $valueCell = $table->addCell(null, $cellStyle);
    //             $valueCell->addText($pair[1], ['size' => 9, 'bold' => true], $textStyle);
    //         }
    //     }
    // }
    private function addTableRowWithBottomBorder($table, $label1, $value1, $label2, $value2)
{
    $row = $table->addRow();

    // First column: Label
$row->addCell(4000, [
    'borderBottomSize' => 6,
    'borderBottomColor' => '000000',
    'borderRightSize' => 6, // Add right border
    'borderRightColor' => '000000',
    'valign' => 'center' // Vertical centering (Y-axis)
])->addText($label1, ['bold' => true, 'size' => 10], [
    'alignment' => 'left',
    'indentation' => ['left' => 100] // Add left indentation
]);


    // First column: Value
    $row->addCell(3000, [
        'borderBottomSize' => 6,
        'borderBottomColor' => '000000',
        'borderRightSize' => 6, // Add right border
        'borderRightColor' => '000000'
    ])->addText($value1, ['size' => 10], ['alignment' => 'left']);

    // Second column: Label
    $row->addCell(4000, [
        'borderBottomSize' => 6,
        'borderBottomColor' => '000000',
        'borderRightSize' => 6, // Add right border
        'borderRightColor' => '000000'
    ])->addText($label2, ['bold' => true, 'size' => 10], ['alignment' => 'left']);

    // Second column: Value
    $row->addCell(3000, [
        'borderBottomSize' => 6,
        'borderBottomColor' => '000000',
        'borderRightSize' => 6, // Add right border
        'borderRightColor' => '000000'
    ])->addText($value2, ['size' => 10], ['alignment' => 'left']);
}

}
