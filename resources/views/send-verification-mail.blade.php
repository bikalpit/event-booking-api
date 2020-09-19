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
          color: #000000c7;">Email Verification</h3>
        </div>
      </div>
      
      <div class="sec-two" style="color: #616161; font-size: 12px; line-height: 2; padding: 10px 50px;">
      <h3>Hello <b>{{$name}}</b>, Here are your email verification mail.</h3>
        <h5>Please click on below link to verify your email</h5>
        <form action="http://eventjio.bi-team.in/api/api/verify-email/{{$verification_token}}">
          <a class="btn btn-primary btn-sm" href="http://eventjio.bi-team.in/api/api/verify-email/{{$verification_token}}">Verify email</a>
        </form>
      </div>
      <div class="devider" style="border-top: 2px dotted #adadad; margin: 10px 65px 0 50px;"></div>
      
    </div>
  </body>
</html>