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
  <title>THARMY CAFE BILL</title>
  <link rel=icon href={{ asset('images/logo.svg') }}>

  <!-- CSS Files -->
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/invoice_pos.css')}}">

  <script src="{{asset('/assets/js/vue.js')}}"></script>
  <style>
    /* Add this new style for the tear line */
 .cut-line {
  text-align: center;
  margin: 20px 0;
  font-size: 18px;
  color: #333;
  font-family: monospace;
  
  padding-top: 45px;
  padding-bottom:10px;
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
          <h2 class="text-center">@{{setting.CompanyName}}</h2>

          <p dir="{{ $languageDirection }}"> 
            <span>{{ __('translate.date') }} : @{{sale.date}} <br></span>
            <span>{{ __('translate.Sale') }}: @{{sale.Ref}} <br></span>
            <span v-show="pos_settings.show_address">{{ __('translate.Address') }} : @{{setting.CompanyAdress}}
              <br></span>
            <span v-show="pos_settings.show_email">{{ __('translate.Email') }} : @{{setting.email}} <br></span>
            <span v-show="pos_settings.show_phone">{{ __('translate.Phone') }} : @{{setting.CompanyPhone}}
              <br></span>
            <span v-show="pos_settings.show_customer">{{ __('translate.Customer') }} : @{{sale.client_name}}
              <br></span>
          </p>
        </div>

        <table class="detail_invoice">
          <tbody>
            <tr v-for="detail_invoice in details">
              <td colspan="3">
                @{{detail_invoice.name}}
                <br v-show="detail_invoice.is_imei && detail_invoice.imei_number !==null">
                <span v-show="detail_invoice.is_imei && detail_invoice.imei_number !==null ">IMEI_SN :
                  @{{detail_invoice.imei_number}}</span>
                <br>
                <span>@{{formatNumber(detail_invoice.quantity,2)}} @{{detail_invoice.unit_sale}} x
                  @{{detail_invoice.price}}</span>
              </td>
              <td class="product_detail_invoice">
                @{{detail_invoice.total}}
              </td>
            </tr>

            <tr class="mt-10" v-show="pos_settings.show_discount">
              <td colspan="3" class="total">{{ __('translate.Discount') }}</td>
              <td class="total text-right">
                <span>@{{sale.discount}}</span>
              </td>
            </tr>

            <tr class="mt-10">
              <td colspan="3" class="total">{{ __('translate.Total') }}</td>
              <td class="total text-right">
                @{{sale.GrandTotal}}</td>
            </tr>

            <tr v-show="isPaid" style="font-size:9px;">
              <td colspan="2">{{ __('translate.Paid') }}</td>
              <td colspan="2" class="text-right">@{{sale.paid_amount}}</td>
            </tr>

            <tr v-show="isPaidLessThanTotal">
              <td colspan="3" class="total">{{ __('translate.Due') }}</td>
              <td class="total text-right">
                @{{sale.due}}
              </td>
            </tr>
          </tbody>
        </table>

        <table class="change mt-3" v-show="isPaid">
          <thead>
            <tr>
              <th class="text-left" colspan="1">{{ __('translate.Paid_by') }}:</th>
              <th class="text-right" colspan="2">{{ __('translate.Amount') }}:</th>
              </th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="payment_pos in payments">
              <td class="text-left" colspan="1">@{{payment_pos.Reglement}}</td>
              <td class="text-right" colspan="2">@{{payment_pos.montant}}
              </td>
            </tr>
          </tbody>
        </table>

        <div id="legalcopy" class="ms-2" v-show="pos_settings.show_note">
          <p class="legal">
            <strong>{{ __('translate.Thank_You_For_Shopping_With_Us_Please_Come_Again') }}</strong>
          </p>
        </div>
      </div>
<div class="cut-line">
------------------------------
</div>

      <!-- Kitchen Copy (Bottom Part) -->
      <div class="kitchen-copy">
        <div class="kitchen-header">
          <h3 class="text-center">KITCHEN COPY</h3>
          <p>
            <span>Order #: @{{sale.Ref}} <br></span>
            <span>Date: @{{sale.date}} <br></span>
            <span v-show="pos_settings.show_customer">Customer: @{{sale.client_name}} <br></span>
          </p>
        </div>
        
        <table class="kitchen-items">
          <thead>
            <tr>
              <th>Item</th>
              <th>Qty</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="detail_invoice in details">
              <td>@{{detail_invoice.name}}</td>
              <td>@{{formatNumber(detail_invoice.quantity,2)}}</td>
            </tr>
          </tbody>
        </table>
        
        <div class="kitchen-footer">
          <p>Order Time: @{{new Date().toLocaleTimeString()}}</p>
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
                var divContents = document.getElementById("invoice-POS").innerHTML;
                var printWindow = window.open("", "_blank", "height=500,width=800");
                
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Invoice</title>
                        <link rel="stylesheet" href="/assets/styles/vendor/pos_print.css">
                        <style>
                            /* Kitchen copy styles */
                            .kitchen-copy {
                                margin-top: 50px;
                                padding-top: 20px;
                                border-top: 2px dashed #000;
                                page-break-before: always;
                            }
                            .kitchen-items {
                                width: 100%;
                                margin-top: 10px;
                            }
                            .kitchen-items th {
                                text-align: left;
                                border-bottom: 1px solid #000;
                            }
                            .kitchen-items td {
                                padding: 3px 0;
                            }
                                  .cut-line {
  text-align: center;
  margin: 20px 0;
  font-size: 12px;
  color: #333;
  font-family: monospace;
  border-top: 1px dashed #000;
  padding-top: 25px;
}

                            .kitchen-header {
                                text-align: center;
                                margin-bottom: 10px;
                            }
                            .kitchen-footer {
                                margin-top: 10px;
                                text-align: center;
                                font-size: 12px;
                            }
                            @media print {
                                body { 
                                    margin: 0;
                                    padding: 0;
                                }
                                #invoice-POS { 
                                    width: 80mm;
                                    margin: 0 auto;
                                }
                                .kitchen-copy {
                                    margin-top: 30mm; /* Space between customer copy and kitchen copy */
                                }
                            }
                        </style>
                        <script>
                            window.onafterprint = function() {
                                window.close();
                            };
                            
                            setTimeout(function() {
                                window.close();
                            }, 3000);
                        <\/script>
                    </head>
                    <body onload="window.print()">${divContents}</body>
                    </html>
                `);
                
                printWindow.document.close();
                window.focus();
            },
            auto_print_pos() {
                var divContents = document.getElementById("invoice-POS").innerHTML;
                var printWindow = window.open('', '_blank', 'height=600,width=800');
                
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Invoice</title>
                        <link rel="stylesheet" href="/assets/styles/vendor/pos_print.css">
                        <style>
                            /* Kitchen copy styles */
                            .kitchen-copy {
                                margin-top: 50px;
                                padding-top: 20px;
                                border-top: 2px dashed #000;
                                page-break-before: always;
                            }
                            .kitchen-items {
                                width: 100%;
                                margin-top: 10px;
                            }
                            .kitchen-items th {
                                text-align: left;
                                border-bottom: 1px solid #000;
                            }
                            .kitchen-items td {
                                padding: 3px 0;
                            }
                            .kitchen-header {
                                text-align: center;
                                margin-bottom: 10px;
                            }
                            .kitchen-footer {
                                margin-top: 10px;
                                text-align: center;
                                font-size: 12px;
                            }
                            @media print {
                                body { 
                                    margin: 0;
                                    padding: 0;
                                }
                                #invoice-POS { 
                                    width: 80mm;
                                    margin: 0 auto;
                                }
                                .kitchen-copy {
                                    margin-top: 30mm; /* Space between customer copy and kitchen copy */
                                }
                            }
                        </style>
                        <script>
                            window.onafterprint = function() {
                                window.close();
                            };
                            
                            setTimeout(function() {
                                if (!window.closed) {
                                    window.close();
                                }
                            }, 3000);
                        <\/script>
                    </head>
                    <body onload="window.print()">${divContents}</body>
                    </html>
                `);
                
                printWindow.document.close();
                window.focus();
            },
        }
    });
  </script>

</body>
</html>