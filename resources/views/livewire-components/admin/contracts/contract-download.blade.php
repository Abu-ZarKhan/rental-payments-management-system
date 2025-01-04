<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 0;
        }
        .page {
            width: 21cm;
            height: 29.7cm;
            padding: 1cm;
            border: 10px solid black;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header .company-details {
            font-size: 10pt;
        }
        .header img {
            width: 100px;
            height: 70px;
        }
        .contact {
            font-size: 8pt;
            color: #4465A1;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            background-color: #1F3864;
            color: white;
            padding: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }
        table th {
            background-color: #ddd;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .signature {
            text-align: center;
            width: 48%;
        }
        .signature .line {
            margin-top: 20px;
            border-top: 1px solid black;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="company-details">
                <p><strong>XAVIER BUSINESS CENTER</strong></p>
                <p>27th Floor Al Saqar Tower,</p>
                <p>Sheikh Zayed Road, Dubai</p>
                <p>Trade Licence: 967319</p>
            </div>
            <div>
                <img src="{{ public_path('images/xavier-logo.png') }}" alt="Logo">
            </div>
        </div>
        <div class="contact">
            <p>Tel: +971 50 172 5600, +971 50 924 5979, Email: management@xavier.ae</p>
            <p>Address: ibne battuta gate building near ibne battuta mall metro station</p>
        </div>

        <div class="section-title">OFFICE LEASE CONTRACT</div>

        <table>
            <tr>
                <th>Company Name:</th>
                <td>{{ $contract->company }}</td>
                <th>Landlord Name:</th>
                <td>{{ $contract->landlord_name }}</td>
            </tr>
            <tr>
                <th>Land Location:</th>
                <td>{{ $contract->land_location }}</td>
                <th>Location:</th>
                <td>{{ $contract->location }}</td>
            </tr>
            <tr>
                <th>Tenant Name:</th>
                <td>{{ $contract->tenant->name }}</td>
                <th>Trade License:</th>
                <td>{{ $contract->trade_license }}</td>
            </tr>
            <tr>
                <th>Nationality:</th>
                <td>{{ $contract->nationality }}</td>
                <th>EID No:</th>
                <td>{{ $contract->eid_no }}</td>
            </tr>
            <tr>
                <th>Contract Start:</th>
                <td>{{ $contract->start_date }}</td>
                <th>Contract End:</th>
                <td>{{ $contract->end_date }}</td>
            </tr>
            <tr>
                <th>Ejari:</th>
                <td>{{ $contract->ejari >= 0 ? 'Yes' : 'No' }}</td>
                <th>Contact No:</th>
                <td>{{ $contract->contact_no }}</td>
            </tr>
        </table>

        <div class="section-title">Payment Details</div>
        <table>
            <tr>
                <th>Date</th>
                <th>Payee Bank</th>
                <th>Amount</th>
                <th>Narration</th>
            </tr>
            @foreach ($contract->tenant->dues->filter(fn($due) => $due->status) as $paidDue)
            <tr>
                <td>{{ formatDate($paidDue->created_at) }}</td>
                <td>{{ $paidDue->payment_method }}</td>
                <td>{{ formatCurrency($paidDue->paid_amount) }}</td>
                <td>{{ $paidDue->note ?: '-' }}</td>
            </tr>
            @endforeach
        </table>

        <div class="section-title">Package Details</div>
        <table>
            <tr>
                <th>Executive Table:</th>
                <td>{{ $building->executive_table }}</td>
                <th>Executive Chair:</th>
                <td>{{ $building->executive_chair }}</td>
            </tr>
            <tr>
                <th>Guest Chair:</th>
                <td>{{ $building->guest_chair }}</td>
                <th>Staff Workstations:</th>
                <td>{{ $building->staff_workstations }}</td>
            </tr>
            <tr>
                <th>Staff Chairs:</th>
                <td>{{ $building->staff_chairs }}</td>
                <th>Cabinet:</th>
                <td>{{ $building->cabinet }}</td>
            </tr>
            <tr>
                <th>Conference Room:</th>
                <td>{{ $building->conference_room }}</td>
                <th>Sofa:</th>
                <td>{{ $building->sofa }}</td>
            </tr>
            <tr>
                <th>Cleaning:</th>
                <td>{{ $building->cleaning }}</td>
                <th>Parking:</th>
                <td>{{ $building->parking }}</td>
            </tr>
            <tr>
                <th>Drinking Water:</th>
                <td>{{ $building->drinking_water }}</td>
                <th>Electricity:</th>
                <td>{{ $building->electricity }}</td>
            </tr>
            <tr>
                <th>Internet:</th>
                <td>{{ $building->internet }}</td>
                <th>Conference Room:</th>
                <td>{{ $building->conference_room }}</td>
            </tr>
        </table>

        <div class="section-title">Account Details</div>
        <table>
            <tr>
                <th>Particulars</th>
                <th>Rent Amount</th>
                <th>Discount / Wave</th>
                <th>Net Amount</th>
            </tr>
            @php
                $actualOfficeRent = $contract->actual_office_rent ?? 0;
                $discount = $contract->discount ?? 0;
                $netRent = $actualOfficeRent - $discount;

                $accountDetails = [
                    ['Actual Office Rent', $actualOfficeRent, $discount, $netRent],
                    ['Admin Fee', '-', '-', $contract->admin_fee ?? '-'],
                    ['Security Deposit', '-', '-', $contract->security_deposit ?? '-'],
                    ['VAT 5%', '-', '-', $contract->vat_amount ?? '-'],
                    ['TOTAL', '-', '-', $contract->total_amount ?? '-'],
                ];
            @endphp

            @foreach ($accountDetails as $detail)
                <tr>
                    <td>{{ $detail[0] }}</td>
                    <td>{{ $detail[1] }}</td>
                    <td>{{ $detail[2] }}</td>
                    <td>{{ $detail[3] }}</td>
                </tr>
            @endforeach
        </table>

        <div class="signature-section">
            <div class="signature">
                <p>Landlord Signature</p>
                <div class="line"></div>
            </div>
            <div class="signature">
                <p>Tenant Signature</p>
                <div class="line"></div>
            </div>
        </div>
    </div>
</body>
</html>