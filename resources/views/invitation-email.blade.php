<html>
  <head>
    <style>
    .sec-one::before{
      content:"";
      border-bottom: 1px solid #000;
    }
    
    </style>
  </head>
  
  <body class="email-template-main" style="background-color: #F3F3F3; margin: 0 auto; font-family: Poppins;">
    <div class="main-div" style="width: 600px; background-color: #fff; box-shadow: 0px 0px 3px 0px rgba(158,158,158,1); border-radius:0px;
    margin: 0 auto; margin-top: 30px; margin-bottom: 30px;">
    
      <div class="sec-one" style="width: 100%; display: inline-flex;">
        <div style="width: 50%;"> 
          <h3 style="font-size: 19px; font-weight: 700; margin: 0 0 0 50px; line-height: 1.5;
          color: #000000c7;">Event Jio Invitation</h3>
        </div>
      </div>
      
      <div class="sec-two" style="color: #616161; font-size: 12px; line-height: 2; padding: 10px 50px;">
        <p>Hello,</p>
        <p>You have invited by {{$name}} for Event Jio.</p><br>
        <h2>Thank You!</h2>
        <div style="display:flex;">
          <a class="btn btn-primary" style="background-color: #31f331;margin-right: 15px;width: 10%;font-size: larger;text-decoration-line: none;color: #3e3737;border-radius: 10%;padding-left: 9px;font-weight: bold;" href="https://api.eventjio.com/api/accept-reject-invitation/{{$token}}/accept">Accept</a>
          <a class="btn btn-danger" style="background-color: #f70404;width: 10%;font-size: larger;text-decoration-line: none;color: #3e3737;border-radius: 10%;padding-left: 10px;font-weight: bold;" href="https://api.eventjio.com/api/accept-reject-invitation/{{$token}}/reject">Reject</a>
        </div>
      </div>
      
    </div>
  </body>
</html>