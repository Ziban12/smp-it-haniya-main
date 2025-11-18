<div class="polman-nav-static-top">
    <div class="float-left">
        <a href="">
            <img src="{{asset('assets/image/palingbaru_logo.png') }}" style="height: 45px ;" />
        </a>
        <body style="background-image: url('logo/Logo.png')"></body>
    </div>

    <div class="polman-menu">
    <div style="padding-top: 15px; margin-right: -30px;">
        @if(Auth::check())
      
            Selamat datang, silakan login.
        @endif
    </div>
</div>
    <div class="polman-menu">
    <div style="padding-top: px; margin-right: -30px;">
       
    </div>
    </div>

    <div class="polman-menu-bar">
        <div class="float-right">
            <div class="fa fa-bars fa-2x" style="margin-top: 9px; cursor: pointer;" aria-hidden="true" data-toggle="collapse" data-target="#menu" aria-expanded="false" aria-controls="menu"></div>
        </div>
    </div>
    
    <div class="mt-5" style="background-color: white; width: 125%; height:10%; position: fixed; left: 0; bottom: 0;border: 3px solid rgba(0, 0, 0, 0.10);">
     <div class="container-fluid">
         <footer class="d-flex flex-wrap pt-3 pb-3 border-top">
             Copyright &copy; <?php echo date('Y'); ?> - MIS Politeknik Astra
         </footer>
     </div>
 </div>
</div>



