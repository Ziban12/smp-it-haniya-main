<div class="polman-nav-static-top">
    <div class="float-left">
        <a href="">
            <img src="{{asset('assets/image/palingbaru_logo.png') }}" style="height: 45px ;" />
        </a>
    </div>
    <div class="polman-menu">
    <div style="padding-top: 15px; margin-right: -30px;">
        @if(Auth::check())
            Hai,&nbsp;<b>{{ Auth::user()->name }}</b> (Koordinator TA)
           
        @else
            Selamat datang, silakan login.
        @endif
    </div>
</div>


    <div class="polman-menu-bar">
        <div class="float-right">
            <div class="fa fa-bars fa-2x" style="margin-top: 9px; cursor: pointer;" aria-hidden="true" data-toggle="collapse" data-target="#menu" aria-expanded="false" aria-controls="menu"></div>
        </div>
    </div>
</div>

<div class="polman-nav-static-right collapse scrollstyle" id="menu">
    <div id="accordions" role="tablist" aria-multiselectable="true">
        <div class="list-group">
            <!-- <a class="list-group-item list-group-item-action polman-username" style="border-radius: 0px; border: none; background-color: #EEE;">
                Hai,&nbsp;<b></b>
            </a> -->

            <a href="/login" class="list-group-item list-group-item-action" style="border-radius: 0px; border: none; padding-left: 23px;">
                <i class="fa fa-sign-out fa-lg fa-pull-left"></i>Logout
            </a>

            <a href='/dashboard' class='list-group-item list-group-item-action' 
                style='border-radius: 0px; border: none; padding-left: 22px; display: inherit;'>
                <i class='fa fa-home fa-lg fa-pull-left'></i>Dashboard
            </a>

            <a href='/kategori_penilaian' class='list-group-item list-group-item-action'
                style='border-radius: 0px; border: none; padding-left: 22px; display: inherit;'>

                <i class="dropdown-menu"></i>Kategori Penilaian
            </a>

           
        

            {{-- <a href='('Mahasiswa'); ?>' class='list-group-item list-group-item-action 
                <?php echo 'Mahasiswa' ? 'polman-menu-active' : ''; ?>'
                style='border-radius: 0px; border: none; padding-left: 22px; display: inherit;'>

                <i class='fa fa-check-circle fa-lg fa-pull-left'></i>Mahasiswa
            </a>

            <a href='('Civitas'); ?>' class='list-group-item list-group-item-action 
                <?php echo 'Civitas' ? 'polman-menu-active' : ''; ?>'
                style='border-radius: 0px; border: none; padding-left: 22px; display: inherit;'>

                <i class='fa fa-check-circle fa-lg fa-pull-left'></i>Civitas
            </a> --> --}}

            <!-- <a href='('DetailSkema'); ?>' class='list-group-item list-group-item-action 
                <?php echo 'Detail Skema' ? 'polman-menu-active' : ''; ?>'
                style='border-radius: 0px; border: none; padding-left: 22px; display: inherit;'>

                <i class='fa fa-check-circle fa-lg fa-pull-left'></i>Detail Skema
            </a> -->
        </div>
    </div>
</div>

