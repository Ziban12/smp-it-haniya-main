@include('layouts.partial.header')
@include('layouts.partial.sidebar')
<div class="polman-adjust5">
  
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
