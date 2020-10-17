<html>
<head>
  <title> </title>
<style>
body{
background-color:#E9E9E9;
font-family: sans-serif;
color: #181818;
}
h3{color:#181818;}
.container{
    width: 750px;
    margin: 0 auto;
    padding: 50px;
}
.mt-6{margin-top: 6px !important;}
.main-wrap{
    background-color: #fff;
    box-shadow: 0px 0px 26px #00000042;
    border-radius: 5px;
    padding-bottom: 15rem;
}
.top-border{border-bottom:4px solid #007BFF; padding:18px;}
.invoice-main{padding:30px;}
.common-add{}
.common-add h3{font-size: 15px;}
.common-add p{color: #595959;
    font-size: 14px;}
.invoice-add-deatils{margin-top: 6rem;margin-bottom: 3rem;}
.payment-stuts{color:#0EAC19;font-weight: 600;}
.invoice-num{margin-right: 14px;}
.invoice-date{margin-right: 14px;}
table{ border-collapse: collapse;width: 100%;}
.bottom-invoice-table table th{text-align:justify;}
.bottom-invoice-table table td{font-size: 14px;}
</style>
</head>
<body>
    <div class="container">
      <div class="main-wrap">
        <div class="top-border"> 
        </div>
        <div class="invoice-main">
          <h3> INVOICE </h3>
          <div class="invoice-add-deatils">
          <table> 
              <tr> 
                <td style="width: 50%;padding: 0px 56px 0px 0px;">
                  <div class="common-add">
                    <h3> BILLED TO </h3>
                    <P> {{$client_name}}, {{$client_address}} </p>
                  </div>
                </td> 
                <td style="width: 50%;padding: 0px 0px 0px 24px;vertical-align: unset;">
                  <div class="common-add">
                    <h3> INVOICE NUMBER DATE OF ISSUE STATUS </h3>
                    <P> <span class="invoice-num"> {{$invoice_no}} </span>  <span class="invoice-date">{{$payment_date}}  </span> <span class="payment-stuts"> {{$payment_status}}  </span> </p>
                  </div>
                </td>
              </tr> 
            </table>
          </div>
          <div class="bottom-invoice-table">
                <table>
                  <tr style="border-bottom: 1px solid #7070706b;border-top: 1px solid #7070706b;height: 40px;font-size: 14px;">
                    <th>ITEM</th>
                    <th>UNIT COST</th>
                    <th>FEE</th>
                    <th>QUANTITY</th>
                    <th>AMOUNT</th>
                  </tr>
                  <tr style="border-bottom: 1px solid #ccc;height: 60px;">
                    <td style="color: #595959;">{{$ticket_name}} <h3 style="margin: 0px;margin-top: 4px;font-size:15px;">{{$event_name}} </h3> </td>
                    <td>${{$price}}</td>
                    <td>$0.00</td>
                    <td>{{$qty}}</td>
                    <td>${{$price*$qty}}</td>
                  </tr>
              </table>
          </div>
        </div>
        <div class="invoice-total-deatils" style="background-color:#F8F8F8;padding: 0px 30px 30px 30px;">
        <table>
                  <tr style="height: 50px;">
                    <td style="width: 20%;">  </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;font-weight: 600;font-size: 15px;vertical-align: bottom;padding-left: 10px;">Subtotal</td>
                    <td style="width: 20%;font-weight: 600;text-align: end;padding-right: 40px;font-size: 15px;font-weight: 600;vertical-align: bottom;"> ${{$sub_total}}</td>
                  </tr>
                  <tr style="height: 60px;">
                    <td style="width: 20%;">  </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;font-weight: 600;font-size: 15px;border-bottom: 1px solid #ccc;padding-left: 10px;">Tax</td>
                    <td style="width: 20%;font-weight: 600;text-align: end;padding-right: 40px;font-size: 15px;font-weight: 600;border-bottom: 1px solid #ccc;"> $0.00</td>
                  </tr>
                  <tr style="height: 60px;">
                    <td style="width: 20%;">  </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;font-weight: 600;font-size: 15px; padding-left: 10px;">Total</td>
                    <td style="width: 20%;font-weight: 600;text-align: end;padding-right: 40px;font-size: 18px;font-weight: 600;color: #007BFF;">  ${{$grand_total}}  </td>
                  </tr>
                  <tr style="height: 40px;">
                    <td style="width: 20%;">  </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;font-weight: 600;font-size: 15px;border-bottom: 1px solid #ccc;padding-left: 10px;vertical-align: baseline;">Payments</td>
                    <td style="width: 20%;font-weight: 600;text-align: end;padding-right: 40px;font-size: 15px;font-weight: 600;border-bottom: 1px solid #ccc;vertical-align: baseline;">
                      @if($payment_status == 'paid')
                      -${{$amount}}
                      @else
                      $0
                      @endif
                    </td>
                  </tr>
                  <tr style="height: 40px;">
                    <td style="width: 20%;">  </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;"> </td>
                    <td style="width: 20%;font-weight: 600;font-size: 15px;padding-left: 10px;">Amount due</td>
                    <td style="width: 20%;font-weight: 600;text-align: end;padding-right: 40px;font-size: 15px;font-weight: 600;">${{$grand_total-$amount}}</td>
                  </tr>
                  
              </table>
        </div>
        
      </div>
</body>
</html>