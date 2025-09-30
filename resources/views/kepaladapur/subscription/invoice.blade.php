<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>
            Invoice
            #{{ str_pad($subscriptionRequest->id_subscription_request, 4, "0", STR_PAD_LEFT) }}
        </title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            @page {
                size: A4;
                margin: 0;
            }

            body {
                font-family: 'Arial', sans-serif;
                background: #f5f5f5;
                padding: 15px;
            }

            .invoice-container {
                max-width: 210mm;
                margin: 0 auto;
                background: white;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .invoice-header {
                background: white;
                color: #000;
                padding: 20px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 3px solid #000;
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .logo img {
                height: 45px;
            }

            .logo-text {
                font-size: 28px;
                font-weight: bold;
                letter-spacing: 1.5px;
            }

            .order-number {
                text-align: right;
            }

            .order-number-label {
                font-size: 10px;
                color: #666;
                margin-bottom: 3px;
                font-weight: 600;
            }

            .order-number-value {
                font-size: 20px;
                font-weight: bold;
            }

            .invoice-body {
                padding: 25px 20px;
            }

            .parties-section {
                display: flex;
                justify-content: space-between;
                margin-bottom: 25px;
            }

            .party {
                flex: 1;
            }

            .party-label {
                font-size: 11px;
                color: #666;
                margin-bottom: 6px;
                font-weight: 600;
            }

            .party-name {
                font-size: 15px;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .party-details {
                font-size: 11px;
                color: #444;
                line-height: 1.5;
            }

            .invoice-meta {
                text-align: right;
                margin-bottom: 20px;
            }

            .meta-label {
                font-size: 13px;
                font-weight: 600;
                color: #666;
                margin-bottom: 3px;
            }

            .meta-value {
                font-size: 11px;
                color: #444;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            .items-table thead {
                background: #000;
                color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }

            .items-table th {
                padding: 10px 12px;
                text-align: left;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                background: #000;
                color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }

            .items-table tbody tr:nth-child(odd) {
                background: #f5f5f5;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }

            .items-table tbody tr:nth-child(even) {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }

            .items-table tbody tr:nth-child(odd) td {
                background: #f5f5f5;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }

            .items-table tbody tr:nth-child(even) td {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }

            .items-table td {
                padding: 12px;
                font-size: 11px;
                vertical-align: top;
            }

            .item-description {
                font-size: 10px;
                color: #666;
                margin-top: 3px;
                line-height: 1.4;
            }

            .summary-section {
                margin-left: auto;
                width: 280px;
                margin-top: 15px;
            }

            .summary-row {
                display: flex;
                justify-content: space-between;
                padding: 7px 0;
                font-size: 11px;
            }

            .summary-row.subtotal {
                border-top: 1px solid #eee;
                padding-top: 10px;
                margin-top: 8px;
                font-weight: 600;
            }

            .summary-row.total {
                border-top: 2px solid #000;
                padding-top: 10px;
                margin-top: 10px;
                font-size: 13px;
                font-weight: bold;
            }

            .payment-section {
                background: #f8f8f8;
                padding: 16px 18px;
                border-radius: 6px;
                margin-top: 20px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }

            .payment-title {
                font-size: 13px;
                font-weight: bold;
                margin-bottom: 10px;
            }

            .payment-details {
                font-size: 11px;
                line-height: 1.6;
            }

            .payment-details strong {
                display: inline-block;
                width: 110px;
            }

            .invoice-footer {
                background: #000;
                color: white;
                padding: 10px 20px;
                text-align: center;
                font-size: 10px;
                margin-top: 25px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }

            .print-button {
                display: block;
                width: 180px;
                margin: 20px auto 0;
                padding: 10px;
                background: #000;
                color: white;
                text-align: center;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                cursor: pointer;
                border: none;
                font-size: 13px;
            }

            .print-button:hover {
                background: #333;
            }

            @media print {
                body {
                    padding: 0;
                    background: white;
                }

                .invoice-container {
                    box-shadow: none;
                    max-width: 100%;
                }

                .print-button {
                    display: none;
                }

                .invoice-body {
                    padding: 20px 25px;
                }

                .payment-section {
                    page-break-inside: avoid;
                }

                /* Force print colors */
                .items-table thead {
                    background: #000 !important;
                    color: white !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .items-table tbody tr:nth-child(odd) {
                    background: #f5f5f5 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .items-table tbody tr:nth-child(even) {
                    background: white !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .invoice-footer {
                    background: #000 !important;
                    color: white !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .invoice-header {
                    border-bottom: 3px solid #000 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .payment-section {
                    background: #f8f8f8 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <!-- Header -->
            <div class="invoice-header">
                <div class="logo">
                    <span class="app-brand-logo demo">
                        <img
                            src="{{ asset("logo_kelola_dapur_black.png") }}"
                            alt="Logo"
                        />
                    </span>
                    <div class="logo-text">INVOICE</div>
                </div>
                <div class="order-number">
                    <div class="order-number-label">ORDER NUMBER:</div>
                    <div class="order-number-value">
                        {{ str_pad($subscriptionRequest->id_subscription_request, 4, "0", STR_PAD_LEFT) }}
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="invoice-body">
                <!-- Parties Section -->
                <div class="parties-section">
                    <div class="party">
                        <div class="party-label">FROM:</div>
                        <div class="party-name">Kelola Dapur</div>
                        <div class="party-details">
                            support@keloladapur.com
                            <br />
                            JL Kauman Barat IV, Palebon, Kec. Pedurungan,
                            <br />
                            Semarang, Jawa Tengah
                        </div>
                    </div>
                    <div class="party">
                        <div class="party-label">INVOICE TO:</div>
                        <div class="party-name">
                            {{ strtoupper($dapur->nama_dapur) }}
                        </div>
                        <div class="party-details">
                            @if ($dapur->alamat)
                                {{ $dapur->alamat }}
                                <br />
                            @endif

                            {{ $dapur->full_wilayah }}
                            @if ($dapur->telepon)
                                <br />
                                {{ $dapur->telepon }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Invoice Meta -->
                <div class="invoice-meta">
                    <div class="meta-label">INVOICE DATE:</div>
                    <div class="meta-value">
                        {{ $subscriptionRequest->tanggal_approval->format("d F Y") }}
                    </div>
                </div>

                <!-- Items Table -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>DESCRIPTION</th>
                            <th style="text-align: center; width: 80px">
                                UNIT PRICE
                            </th>
                            <th style="text-align: center; width: 50px">QTY</th>
                            <th style="text-align: right; width: 100px">
                                TOTAL
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>
                                    {{ $subscriptionRequest->package->nama_paket }}
                                </strong>
                                <div class="item-description">
                                    {{ $subscriptionRequest->package->deskripsi }}
                                    <br />
                                    Durasi:
                                    {{ $subscriptionRequest->package->durasi_text }}
                                </div>
                            </td>
                            <td style="text-align: right">
                                {{ $subscriptionRequest->formatted_harga_asli }}
                            </td>
                            <td style="text-align: center">1</td>
                            <td style="text-align: right">
                                {{ $subscriptionRequest->formatted_harga_asli }}
                            </td>
                        </tr>
                        @if ($subscriptionRequest->promoCode)
                            <tr>
                                <td>
                                    <strong>Diskon Promo</strong>
                                    <div class="item-description">
                                        Kode:
                                        {{ $subscriptionRequest->promoCode->kode_promo }}
                                        ({{ $subscriptionRequest->promoCode->persentase_diskon }}%)
                                    </div>
                                </td>
                                <td style="text-align: right">
                                    -{{ $subscriptionRequest->formatted_diskon }}
                                </td>
                                <td style="text-align: center">1</td>
                                <td style="text-align: right">
                                    -{{ $subscriptionRequest->formatted_diskon }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td>
                                <strong>Biaya Administrasi</strong>
                                <div class="item-description">
                                    ID Dapur: {{ $dapur->id_dapur }}
                                </div>
                            </td>
                            <td style="text-align: right">
                                Rp
                                {{ number_format($dapur->id_dapur, 0, ",", ".") }}
                            </td>
                            <td style="text-align: center">1</td>
                            <td style="text-align: right">
                                Rp
                                {{ number_format($dapur->id_dapur, 0, ",", ".") }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Summary -->
                <div class="summary-section">
                    <div class="summary-row subtotal">
                        <span>SUBTOTAL</span>
                        <span>
                            {{ $subscriptionRequest->formatted_harga_asli }}
                        </span>
                    </div>
                    @if ($subscriptionRequest->diskon > 0)
                        <div class="summary-row">
                            <span>Diskon</span>
                            <span>
                                -{{ $subscriptionRequest->formatted_diskon }}
                            </span>
                        </div>
                    @endif

                    <div class="summary-row">
                        <span>Biaya Admin</span>
                        <span>
                            Rp
                            {{ number_format($dapur->id_dapur, 0, ",", ".") }}
                        </span>
                    </div>
                    <div class="summary-row">
                        <span>Tax</span>
                        <span>-</span>
                    </div>
                    <div class="summary-row total">
                        <span>TOTAL</span>
                        <span>
                            {{ $subscriptionRequest->formatted_harga_final }}
                        </span>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="payment-section">
                    <div class="payment-title">SEND PAYMENT TO:</div>
                    <div class="payment-details">
                        <strong>Bank:</strong>
                        BRI
                        <br />
                        <strong>Account Name:</strong>
                        Mihammad Khoirul Anam
                        <br />
                        <strong>Account No:</strong>
                        067801021372500
                        <br />
                        <strong>Pay by:</strong>
                        {{ $subscriptionRequest->tanggal_approval->format("d F Y") }}
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="invoice-footer">Created by Kelola Dapur</div>
        </div>

        <button class="print-button" onclick="window.print()">
            Print Invoice
        </button>
    </body>
</html>
