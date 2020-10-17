<html>
<head>
  <title> </title>
<style>
body{
background-color:#E9E9E9;
font-family: sans-serif;
}
h3{color:#181818;}
p{font-size:14px;}
.container{
    width: 750px;
    margin: 0 auto;
    padding: 50px;
}
.mt-6{margin-top: 6px !important;}
.main-wrap{
    padding:30px;
    background-color: #fff;
    box-shadow: 0px 0px 26px #00000042;
    border-radius: 5px;
    padding-bottom: 4rem;
}
.ticket-main{
    border: 1px dashed #595959;
    padding: 14px;
    width: 350px;
}
.ticket-barcode{
    height: 200px;
    width: 200px;
    background-color: #e9e9e9;    
    margin-bottom: 42px;
}
.common-tickets-deatils{margin: 30px 0px;}
.common-tickets-deatils p{
    margin: 0px;
    font-size: 12px;
    color: #595959;
}
.common-tickets-deatils h3{
    margin: 0px;
    font-size: 20px;
    margin-top: 6px;
    color: #181818;
}
table{ border-collapse: collapse;width: 100%;border: 1px solid #ccc;}
table th{text-align:justify;padding: 12px 18px;border-left: 1px solid #ccc;}
table td{padding: 12px 18px;border-left: 1px solid #ccc;}
.events-deatils-main p {    MARGIN: 0px;
    margin-bottom: 4px;}
.events-deatils-main span {font-weight: 600;}
</style>
</head>
<body>
    <div class="container">
      <div class="main-wrap">
      <div class="top-order-confo-main">
          <h3 style="margin-top: 40px;"> Thank you for your order </h3>
          <p style="color: #181818;font-size:14px; letter-spacing: 0.8;margin-top: 26px;"> Dear {{$client_name}} <br /></p>
          <p style="color: #181818;width: 70%;font-size:14px;margin-top:30px;    letter-spacing: 0.8;"> Thank you for the order you recently placed for tickets to the event {{$event_name}} (on {{date('l d F Y', strtotime($start_date))}}).</p>
      </div>
      <h3 style="margin: 22px 0px;"> Your tickets </h3>
        <div class="ticket-main">
            <div class="ticket-barcode">
            </div>
            <div class="common-tickets-deatils">
            <p> TICKET CODE </p>
            <h3> {{$ticket_code}} </h3>
            </div>
            <div class="common-tickets-deatils">
            <p> TICKET TYPE </p>
            <h3> {{$ticket_name}} </h3>
            </div>
            <div class="common-tickets-deatils">
            <p> ATTENDEE NAME </p>
            <h3> {{$attendee_name}} </h3>
            </div>
            <div class="common-tickets-deatils">
            <p> EVENT</p>
            <h3> {{$event_name}}  </h3>
            <p class="mt-6"> {{date('l d F Y h:i A', strtotime($start_date))}} </p> 
            </div>
        </div>
        <div class="bottom-order-summary">
          <p style="width:70%;margin: 24px 0px;">Please bring these tickets with you to the event. Personal details such as your address may be used for verification on entry. </p>
          <h3>Order summary </h3>
          <div class="order-table-wrap">
            <table>
                  <tr style="border-bottom: 1px solid #7070706b;border-top: 1px solid #7070706b;height: 40px;font-size: 14px;">
                    <th>Item</th>
                    <th>Price</th>
                    <th>Fee</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                  </tr>
                  <tr style="height: 54px;">
                    <td>{{$ticket_name}}  </td>  
                    <td>${{$price}}</td>
                    <td>$0.00</td>
                    <td>{{$qty}}</td>
                    <td>${{$sub_total}}</td>
                  </tr>
                  <tr style="border-bottom: 1px solid #ccc;height: 40px;">
                    <td style="font-weight: 700;">Grand Total  </td>  
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="font-weight: 700;">${{$grand_total}}</td>
                  </tr>
              </table>
          </div>
          <h3 style="margin-top: 32px;">Event details</h3>
          <div class="events-deatils-main">
            <p> <span> Event name: </span> {{$event_name}} </p>
            <p> <span> Event date: </span> {{date('l d F Y h:i A', strtotime($start_date))}} - {{date('l d F Y h:i A', strtotime($end_date))}} </p>
            <p> <span> Venue: </span> {{$venue}} </p>
          </div>
        </div>
    </div>
</body>
</html>