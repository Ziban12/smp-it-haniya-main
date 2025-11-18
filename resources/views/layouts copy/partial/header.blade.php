<meta charset="utf-8" />
<meta name="viewport" content="width=device-width" />

<link rel="shortcut icon" href="{{ asset('assets/image/Logo_ASTRAtech.png') }}">
<link href="{{ asset('assets/Plugins/bootstrap-4.0.0-alpha.6-dist/css/bootstrap.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/Plugins/font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/Content/jquery.fancybox.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/Content/bootstrap-select.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/Styles/Style.css') }}" rel="stylesheet" />
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


<script src="{{ asset('assets/Scripts/tether/tether.min.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script src="{{ asset('assets/Scripts/jquery-3.5.1.js') }}"></script>
<!-- jangan lupa menambahkan script js sweet alert di bawah ini  -->
<script src="{{ asset('assets/Scripts/jquery.table2excel.min.js') }}"></script>
{{-- <script src="{{ asset('assets/Plugins/sweetalert2-10.15.7/sweetalert2.all.min.js') }}"></script> --}}
<script src="{{ asset('assets/Scripts/jquery-ui-1.12.1.min.js') }}"></script>
<script src="{{ asset('assets/Plugins/bootstrap-4.0.0-alpha.6-dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/Scripts/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/Scripts/jquery.fancybox.pack.js') }}"></script>
<script src="{{ asset('assets/Scripts/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/Scripts/LetterAvatar.js') }}"></script>
<script src="{{ asset('assets/Scripts/tableToExcel.js') }}"></script>
<script src="{{ asset('assets/Scripts/aos.js') }}"></script>
<script src="{{ asset('assets/Scripts/xlsx.full.min.js') }}"></script>

<script type="text/javascript">
    $(function () {
        $("[data-toggle=tooltip").tooltip();
    })

    $(function () {
        $('[rel="tooltip"]').tooltip()
    })

    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    function redirectNotifikasi() {
        // window.location.href = 'Page_Notifikasi.aspx';
    }

    function sentValidation(input) {
        // $(input).addClass('disabled');
        // $(input).text('Mohon tunggu..');
    }

    function pageLoad(sender, args) {
        // $('.selectpicker').selectpicker();
        // katweKibsAvatar.init({
        //     dataChars: 2
        // });
    }

    function refreshChart() {
        // try { $('#container1').highcharts().setSize(undefined, undefined, false); } catch (err) { }
        // try { $('#container2').highcharts().setSize(undefined, undefined, false); } catch (err) { }
        // try { $('#container3').highcharts().setSize(undefined, undefined, false); } catch (err) { }
    }
</script>

<script src="{{ asset('assets/Scripts/jquery.floatThead.js') }}"></script>

<style>
    .mce-branding-powered-by {
        display: none;
    }

    table {
        border-top: none;
        border-bottom: none;
        background-color: #FFF;
        white-space: nowrap;
    }

    .table-striped tbody tr:nth-of-type(2n+1) {
        background-color: #FFF;
    }

    .table-striped tbody tr:nth-of-type(2n), thead {
        background-color: #ECECEC;
    }

    .table-striped tbody tr.pagination-ys {
        background-color: #FFF;
    }

    .bottom-wrapper {
        margin-top: 1em;
    }

    div.dataTables_filter > label > input {
        
    }

    .btn:hover {
      cursor: pointer;
    }
</style>

{{-- <script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap4.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css">

<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script> --}}