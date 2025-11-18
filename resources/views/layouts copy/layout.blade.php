@include('layouts.partial.header')
@include('layouts.partial.sidebar')
<div class="polman-adjust5">
    <ol class="breadcrumb polman-breadcrumb">
        <li class="breadcrumb-item"><a href="https://sia.polman.astra.ac.id/sso" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Menuju Hal SSO">Sistem Projek Sidang Akhir</a></li>
        <li class="breadcrumb-item">{{$title}}</li>
    </ol>
    <hr />
    @yield('konten')

</div>

<script>    
    $(document).ready(function () {
        // Fungsi untuk memperbarui judul breadcrumb
        function updateBreadcrumb(title) {
            $('#breadcrumb-item').text(title);
        }

        // Menggunakan event click pada tautan untuk memanggil fungsi updateBreadcrumb
        $('.breadcrumb-item a').click(function () {
            var title = $(this).text();
            updateBreadcrumb(title);
        });
    });
</script>
