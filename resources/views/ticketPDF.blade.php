<html>
<head>
  <title> </title>
<style>
body{
background-color:#E9E9E9;
font-family: sans-serif;
}
h3{color:#181818;}
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
.bottom-deatils{margin-top: 8rem;}
.bottom-deatils p{color: #181818;
  line-height: 1.4;
   font-size: 14px;}
</style>
</head>
<body>
    <div class="container">
      <div class="main-wrap">
        <div class="ticket-main">
            <h3> Ticket 1 of 1 </h3>
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
            <h3> {{$client_name}} </h3>
            </div>
            <div class="common-tickets-deatils">
            <p> EVENT</p>
            <h3> {{$event_name}}  </h3>
            <p class="mt-6"> {{date('l d F Y', strtotime($event_date))}} </p> 
            </div>
        </div>
        <div class="bottom-deatils">
          <p> Organizing an event? Sell tickets online with Webjio Ticket. Low fees. No fuss. <br /> www.webjioticket.com </p>
        </div>
    </div>
</body>
</html>