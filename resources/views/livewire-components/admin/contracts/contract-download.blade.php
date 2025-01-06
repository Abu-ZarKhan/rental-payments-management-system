<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract</title>
    <script src="https://cdn.tailwindcss.com/2.0.2"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 0;
        }

        .text-center {
            text-align: center;
        }

        .mx-auto {
            margin: 0 auto;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mt-0 {
            margin-top: 0 !important;
        }

        .fs-12 {
            font-size: 12px;
        }

        .page {
            width: 21cm;
            height: 29.7cm;
            padding: 0.1cm;
            border: 1px solid #000;

            box-sizing: border-box;
        }

        .header {
            display: flex;
            /* justify-content: space-between; */
            align-items: center;
            margin-bottom: 20px;
        }

        .header .company-details {
            font-size: 10pt;
        }

        .header img {
            width: 130px;
            height: 100px;
        }

        .contact {
            font-size: 8pt;
            color: #4465A1;
            margin-bottom: 20px;
        }

        .logo-div {
            width: 500px;
            display: flex;
            justify-content: center;
        }

        .fw-bold {
            font-weight: bold;
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

        table th,
        table td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        table th {
            background-color: #ddd;
            width: 184px;
            ;
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

        .signature p {
            padding-bottom: 50px;
        }

        .signature .line {
            margin-top: 20px;
            border-top: 1px solid black;
            padding-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="page mx-auto">
        <div class="header">
            <span class="company-details " style="padding-top:30px; ">
                <p class="fw-bold"><strong>XAVIER BUSINESS CENTER</strong></p>
                <p class="fw-bold">27th Floor Al Saqar Tower,</p>
                <p class="fw-bold">Sheikh Zayed Road, Dubai</p>
                <p class="fw-bold">Trade Licence: 967319</p>
            </span>
            <div class="logo-div">

                <img src="{{ asset('images/xavier-logo.png') }}" alt="Logo" class="w-24 h-16">

            </div>
        </div>
        <div class="contact mb-0">
            <p class="mb-0 fs-12">Tel: +971 50 172 5600, +971 50 924 5979, Email: management@xavier.ae Address: Ibn
                Battuta Gate Building near Ibn Battuta Mall Metro Station</p>

        </div>

        <div class="section-title mb-0 mt-0" style="text-decoration:underline;">OFFICE LEASE CONTRACT</div>

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
        <div class="section-title mb-0">Account Details</div>
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
                    ['VAT 5', '-', '-', $contract->vat ?? '-'],
                    ['Parking Card Fee', '-', '-', $contract->parking_card_fee ?? '-'],
                    ['Commission', '-', '-', $contract->commission ?? '-'],
                    ['Ejari', '-', '-', $contract->ejari ?? '-'],
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
        <div class="section-title mb-0">Payment Details</div>
        <table>
            <tr>
                <th class="text-center">Date</th>
                <th class="text-center">Payee Bank</th>
                <th class="text-center">Amount</th>
                <th class="text-center">Narration</th>
            </tr>
            @foreach ($contract->tenant->dues->filter(fn($due) => $due->status) as $paidDue)
                <tr>
                    <td class="text-center">{{ formatDate($paidDue->created_at) }}</td>
                    <td class="text-center">{{ ucfirst($paidDue->payment_method ?: '-') }}</td>
                    <td class="text-center">{{ formatCurrency($paidDue->paid_amount ?: '-') }}</td>
                    <td class="text-center">{{ $paidDue->note ?: '-' }}</td>
                </tr>
            @endforeach
        </table>

        <div class="section-title mb-0">Package Details</div>
        <table>
            <tr>
                <th>Executive Table:</th>
                <td class="text-center" class="text-center">{{ $contract->building->executive_table }}</td>
                <th>Executive Chair:</th>
                <td class="text-center" class="text-center">{{ $contract->building->executive_chair }}</td>
            </tr>
            <tr>
                <th>Guest Chair:</th>
                <td class="text-center">{{ $contract->building->guest_chair }}</td>
                <th>Staff Workstations:</th>
                <td class="text-center">{{ $contract->building->staff_workstations }}</td>
            </tr>
            <tr>
                <th>Staff Chairs:</th>
                <td class="text-center">{{ $contract->building->staff_chairs }}</td>
                <th>Cabinet:</th>
                <td class="text-center">{{ $contract->building->cabinet }}</td>
            </tr>
            <tr>
                <th>Conference Room:</th>
                <td class="text-center">{{ $contract->building->conference_room ?: '-' }}</td>
                <th>Sofa:</th>
                <td class="text-center">{{ $contract->building->sofa ?: '-' }}</td>
            </tr>
            <tr>
                <th>Cleaning:</th>
                <td class="text-center">{{ $contract->building->cleaning ?: '-' }}</td>
                <th>Parking:</th>
                <td class="text-center">{{ $contract->building->parking ?: '-' }}</td>
            </tr>
            <tr>
                <th>Drinking Water:</th>
                <td class="text-center">{{ $contract->building->drinking_water ?: '-' }}</td>
                <th>Electricity:</th>
                <td class="text-center">{{ $contract->building->electricity ?: '-' }}</td>
            </tr>
            <tr>
                <th>Internet:</th>
                <td class="text-center">{{ $contract->building->internet ?: '-' }}</td>
                <th>Conference Room:</th>
                <td class="text-center">{{ $contract->building->conference_room ?: '-' }}</td>
            </tr>
        </table>


        <table>
            <div class="signature-section" style="margin-bottom:20px;">
                <div class="signature">
                    <p>Landlord Signature</p>
                    <div class="line"></div>
                </div>
                <div class="signature">
                    <p>Tenant Signature</p>
                    <div class="line"></div>
                </div>
            </div>
        </table>
        <div class="text-center my-5">
            {{-- <button onclick="printContract()"
                style="background:#1F3864;color:#fff;outline:none;border:none;padding:10px;border-radius:10px;margin-bottom:10px;"
                class="bg-blue-500 text-white px-4 py-2 rounded">Print Contract</button> --}}
            <button onclick="window.print();" class="bg-blue-500 text-white px-4 py-2 rounded">Print Contract</button>

        </div>
    </div>


</body>


</html>
