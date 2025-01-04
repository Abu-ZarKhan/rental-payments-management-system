<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Download</title>
    <script src="https://cdn.tailwindcss.com/2.0.2"></script>
</head>
<body class="bg-gray-100 text-sm font-sans">
    <div class="max-w-5xl mx-auto bg-white border border-gray-300 shadow-md p-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center border-b border-gray-400 pb-4 mb-4">
            <div>
                <p class="text-xl font-bold">XAVIER BUSINESS CENTER</p>
                <p>27th Floor Al Saqar Tower,</p>
                <p>Sheikh Zayed Road, Dubai</p>
                <p>Trade Licence: 967319</p>
            </div>
            <div>
                <img src="{{ asset('images/xavier-logo.png') }}" alt="Xavier Logo" class="h-20">
            </div>
        </div>

        <!-- Contract Details Table -->
        <div class="mb-6">
            <table class="w-full text-left border-collapse border border-gray-400">
                <tr class="bg-gray-100">
                    <th class="border border-gray-400 px-4 py-2">Company Name</th>
                    <td class="border border-gray-400 px-4 py-2">{{ $contract->company }}</td>
                    <th class="border border-gray-400 px-4 py-2">Landlord Name</th>
                    <td class="border border-gray-400 px-4 py-2">{{ $contract->landlord_name }}</td>
                </tr>
                <tr>
                    <th class="border border-gray-400 px-4 py-2">Tenant Name</th>
                    <td class="border border-gray-400 px-4 py-2">{{ $contract->tenant->name }}</td>
                    <th class="border border-gray-400 px-4 py-2">Trade License</th>
                    <td class="border border-gray-400 px-4 py-2">{{ $contract->trade_license }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
