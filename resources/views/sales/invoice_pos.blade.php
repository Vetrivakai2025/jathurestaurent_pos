<?php

if(isset($_COOKIE['language']) &&  $_COOKIE['language'] == 'ar') {
    $languageDirection = 'rtl' ;
} else {
    $languageDirection = 'ltr' ;
}
		
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>INVOICE BILL</title>
  <link rel=icon href={{ asset('images/logo.svg') }}>

  <!-- CSS Files -->
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/invoice_pos.css')}}">

  <script src="{{asset('/assets/js/vue.js')}}"></script>
  <style>    /* Thermal Printer Specific Styles */
    @media print {
        * {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        body {
            width: 80mm !important;
            max-width: 80mm !important;
            margin: 0 !important;
            padding: 5px !important;
            font-size: 14px !important;
            line-height: 1.2 !important;
            font-family: "Arial Narrow", Arial, sans-serif !important;
        }
        
        #invoice-POS {
            width: 78mm !important;
            max-width: 78mm !important;
            margin: 0 auto !important;
        }
        
        /* Customer Copy - First part */
        .customer-copy {
            display: block;
            page-break-after: always;
            margin-bottom: 10px !important;
        }
        
        /* Hide cut line when printing */
        .cut-line {
            display: none !important;
        }
        
        /* Kitchen Copy - Second part (will print on next paper) */
        .kitchen-copy {
            display: block;
            page-break-before: always;
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        
        /* Force paper cut between copies */
        .customer-copy:after {
            content: "";
            display: block;
            height: 20px;
        }
        
        /* Hide print button */
        .hidden-print {
            display: none !important;
        }
        
        /* Optimize tables for thermal */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 5px 0 !important;
        }
        
        td, th {
            padding: 3px 2px !important;
            font-size: 13px !important;
        }
        
        /* Reduce spacing */
        .info, .kitchen-header {
            margin-bottom: 8px !important;
        }
        
        h2, h3 {
            margin: 5px 0 !important;
            font-size: 16px !important;
        }
        
        p {
            margin: 3px 0 !important;
        }
    }

    /* Screen Styles */
    .cut-line {
        text-align: center;
        margin: 15px 0;
        font-size: 18px;
        color: #333;
        font-family: monospace;
        border-top: 2px dashed #000;
        padding-top: 20px;
    }

    .info img {
            display: block !important;
            margin: 0 auto 5px !important;
            max-height: 50px !important;
            text-align: center !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

    p.legal {
        text-align: center;
        margin: 10px 0;
    }

    table.detail_invoice {
        margin-bottom: 15px;
        width: 100%;
    }

    /* Kitchen copy styles */
    .kitchen-copy {
        margin-top: 30px;
        padding-top: 20px;
    }

    .kitchen-header {
        text-align: center;
        margin-bottom: 15px;
    }

    .kitchen-header h3 {
        margin: 0 0 10px 0;
        font-size: 18px;
        font-weight: bold;
    }

    .kitchen-items {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
    }

    .kitchen-items th {
        text-align: left;
        border-bottom: 2px solid #000;
        padding: 8px 0;
        font-weight: bold;
    }

    .kitchen-items td {
        padding: 6px 0;
        border-bottom: 1px dashed #ccc;
    }

    .kitchen-footer {
        margin-top: 15px;
        text-align: center;
        font-size: 12px;
    }

  </style>
</head>

<body>

  <div id="in_pos">
    <div class="hidden-print">
      <a @click="print_pos()" class="btn btn-primary"> {{ __('translate.print') }}</a>
      <br>
    </div>
    <div id="invoice-POS">
    <!-- Customer Copy (Top Part) -->
    <div class="customer-copy">
        <div class="info">
            <img src="/images/logo/jeyalogo.png" alt="" style="max-height: 50px; margin-bottom: 8px;">
            <h2 class="text-center" style="margin: 5px 0;">@{{setting.CompanyName}}</h2>
            <p style="margin: 3px 0; font-size: 12px;">
                <span>{{ __('translate.Sale') }}: @{{sale.Ref}}<br></span>
                <span v-show="pos_settings.show_address">{{ __('translate.Address') }}: @{{setting.CompanyAdress}}<br></span>
                <span v-show="pos_settings.show_email">{{ __('translate.Email') }}: @{{setting.email}}<br></span>
                <span v-show="pos_settings.show_phone">{{ __('translate.Phone') }}: @{{setting.CompanyPhone}}<br></span>
                <span v-show="pos_settings.show_customer">{{ __('translate.Customer') }}: @{{sale.client_name}}<br></span>
                <span>{{ __('translate.date') }}: @{{sale.date}}</span>
            </p>
        </div>

        <table class="detail_invoice" style="width: 100%; margin: 8px 0;">
            <tbody>
                <tr v-for="detail_invoice in details" style="vertical-align: top;">
                    <td colspan="3" style="padding: 4px 2px;">
                        @{{detail_invoice.name}}
                        <br v-show="detail_invoice.is_imei && detail_invoice.imei_number !==null">
                        <span v-show="detail_invoice.is_imei && detail_invoice.imei_number !==null" style="font-size: 11px;">
                            IMEI_SN: @{{detail_invoice.imei_number}}
                        </span>
                        <br>
                        <span style="font-size: 11px;">
                            @{{formatNumber(detail_invoice.quantity,2)}} @{{detail_invoice.unit_sale}} x @{{detail_invoice.price}}
                        </span>
                    </td>
                    <td class="product_detail_invoice" style="text-align: right; padding: 4px 2px;">
                        @{{detail_invoice.total}}
                    </td>
                </tr>

                <!-- Totals section -->
                <tr v-show="pos_settings.show_discount" style="border-top: 1px dashed #000;">
                    <td colspan="3" class="total" style="padding: 4px 2px;">{{ __('translate.Discount') }}</td>
                    <td class="total text-right" style="padding: 4px 2px;">
                        <span>@{{sale.discount}}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class="total" style="padding: 4px 2px; font-weight: bold;">{{ __('translate.Total') }}</td>
                    <td class="total text-right" style="padding: 4px 2px; font-weight: bold;">
                        @{{sale.GrandTotal}}
                    </td>
                </tr>

                <tr v-show="isPaid" style="font-size: 11px;">
                    <td colspan="2" style="padding: 3px 2px;">{{ __('translate.Paid') }}</td>
                    <td colspan="2" class="text-right" style="padding: 3px 2px;">@{{sale.paid_amount}}</td>
                </tr>

                <tr v-show="isPaidLessThanTotal">
                    <td colspan="3" class="total" style="padding: 4px 2px;">{{ __('translate.Due') }}</td>
                    <td class="total text-right" style="padding: 4px 2px;">
                        @{{sale.due}}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="change" v-show="isPaid" style="width: 100%; margin: 8px 0; font-size: 12px;">
            <thead>
                <tr>
                    <th class="text-left" colspan="1" style="padding: 3px 2px;">{{ __('translate.Paid_by') }}:</th>
                    <th class="text-right" colspan="2" style="padding: 3px 2px;">{{ __('translate.Amount') }}:</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="payment_pos in payments">
                    <td class="text-left" colspan="1" style="padding: 3px 2px;">@{{payment_pos.Reglement}}</td>
                    <td class="text-right" colspan="2" style="padding: 3px 2px;">@{{payment_pos.montant}}</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: center; margin: 10px 0;" v-show="pos_settings.show_note">
            <p class="legal" style="margin: 5px 0;">
                <strong>{{ __('translate.Thank_You_For_Shopping_With_Us_Please_Come_Again') }}</strong>
            </p>
        </div>
    </div>
           
    <!-- Cut line (visible on screen only) -->
    <div class="cut-line">
        --- CUT HERE ---
    </div>

    <!-- Kitchen Copy (Bottom Part) -->
    <div class="kitchen-copy">
        <div class="kitchen-header">
            <h3 class="text-center">KITCHEN ORDER TICKET</h3>
            <p style="margin: 3px 0; font-size: 12px;">
                <span>Order #: @{{sale.Ref}}<br></span>
                <span>Date: @{{sale.date}}<br></span>
                <span style="font-weight: bold; font-size: 13px;">Table: @{{ sale.table_num }}<br></span>
                <span v-show="pos_settings.show_customer">Customer: @{{sale.client_name}}<br></span>
            </p>
        </div>
        
        <table class="kitchen-items">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 5px 0; border-bottom: 2px solid #000;">Item</th>
                    <th style="text-align: right; padding: 5px 0; border-bottom: 2px solid #000;">Qty</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="detail_invoice in details">
                    <td style="padding: 4px 0; border-bottom: 1px dashed #ccc;">@{{detail_invoice.name}}</td>
                    <td style="text-align: right; padding: 4px 0; border-bottom: 1px dashed #ccc;">@{{formatNumber(detail_invoice.quantity,2)}}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="kitchen-footer">
            <p style="margin: 5px 0; font-size: 11px;">Order Time: @{{new Date().toLocaleTimeString()}}</p>
        </div>
    </div>
</div>
  </div>

  <script src="{{asset('/assets/js/jquery.min.js')}}"></script>

  <script>
    
    var app = new Vue({
        el: '#in_pos',
        data: {
            payments: @json($payments),
            details: @json($details),
            pos_settings: @json($pos_settings),
            sale: @json($sale),
            setting: @json($setting),
        },
        mounted() {
            if (this.pos_settings.is_printable || window.location.search.includes('autoprint=true')) {
                this.auto_print_pos();
            }
        },
        computed: {
            isPaid() {
                return parseFloat(this.sale.paid_amount) > 0;
            },
            isPaidLessThanTotal() {
                return parseFloat(this.sale.paid_amount) < parseFloat(this.sale.GrandTotal);
            }
        },
        methods: {
            formatNumber(number, dec) {
                const value = (typeof number === "string" ? number : number.toString()).split(".");
                if (dec <= 0) return value[0];
                let formated = value[1] || "";
                if (formated.length > dec)
                    return `${value[0]}.${formated.substr(0, dec)}`;
                while (formated.length < dec) formated += "0";
                return `${value[0]}.${formated}`;
            },
            print_pos() {
        // For thermal printers, use simple window.print()
        window.print();
    },

    auto_print_pos() {
        // Direct print for thermal printer
        setTimeout(() => {
            window.print();
            
            // Close window if it's a popup
            if (window.opener) {
                setTimeout(() => {
                    window.close();
                }, 1000);
            }
        }, 500);
    },
        }
    });
  </script>
<script>
// Auto print only if autoprint parameter is present
if (window.location.search.includes('autoprint=true')) {
    // Set a flag to track if we've already printed
    if (!sessionStorage.getItem('hasAutoPrinted')) {
        sessionStorage.setItem('hasAutoPrinted', 'true');
        
        // Wait for content to load, then print
        setTimeout(() => {
            window.print();
            
            // Clear the flag after a delay
            setTimeout(() => {
                sessionStorage.removeItem('hasAutoPrinted');
            }, 1000);
        }, 1000);
    }
}

// Listen for print events
window.addEventListener('afterprint', function(event) {
    console.log('Print dialog closed');
    
    // If this is a popup window, close it after printing
    if (window.opener && !window.opener.closed) {
        setTimeout(() => {
            window.close();
        }, 500);
    }
});

// Alternative approach for browsers that support beforeprint/afterprint
window.onbeforeprint = function() {
    console.log('Print dialog opened');
};

window.onafterprint = function() {
    console.log('Print dialog closed');
    
    // Close window if it was opened for printing
    if (window.opener && !window.opener.closed) {
        setTimeout(() => {
            window.close();
        }, 500);
    }
};
</script>
</body>
</html>