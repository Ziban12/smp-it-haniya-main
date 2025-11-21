@extends('layouts.app')

@section('title', 'Articles')
@section('page-title', 'Article Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-newspaper"></i> Article List
        </h5>

        <a href="{{ route('employee.articles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Article
        </a>
    </div>

    <div class="card-body">

        @if ($articles->count() > 0)

            <div class="table-responsive">
                <table id="articlesTable" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Article ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Tags</th>
                            <th>Created By</th>
                            <th class="text-center" width="140">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($articles as $article)
                            <tr>
                                <td>
                                    <span class="badge bg-info">{{ $article->article_id }}</span>
                                </td>

                                <td>{{ $article->title }}</td>

                                <td>
                                    <span class="badge bg-secondary">{{ $article->article_type }}</span>
                                </td>

                                <td>
                                    @if ($article->status === 'Published')
                                        <span class="badge bg-success">{{ $article->status }}</span>
                                    @elseif ($article->status === 'Draft')
                                        <span class="badge bg-warning">{{ $article->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $article->status }}</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-info">{{ $article->tag_count ?? 0 }} Tags</span>
                                </td>

                                <td>{{ $article->created_by }}</td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">

                                        {{-- Manage tags --}}
                                        <a href="{{ route('employee.articles.tag', $article->article_id) }}"
                                           class="btn btn-info"
                                           title="Manage Tags">
                                            <i class="fas fa-tags"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('employee.articles.edit', $article->article_id) }}"
                                           class="btn btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('employee.articles.destroy', $article->article_id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="btn btn-danger delete-confirm"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @else

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No articles found.
                <a href="{{ route('employee.articles.create') }}">Create one now!</a>
            </div>

        @endif

    </div>
</div>
@endsection


@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- DATATABLE --}}
<script>
$(document).ready(function () {
    $('#articlesTable').DataTable({
        ordering: true,
        pageLength: 10,
    });
});
</script>

{{-- SWEETALERT SUCCESS --}}
@if (session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Sukses!',
    text: '{{ session("success") }}',
    timer: 2000,
    showConfirmButton: false
});
</script>
@endif

{{-- SWEETALERT ERROR --}}
@if (session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '{{ session("error") }}'
});
</script>
@endif

{{-- SWEETALERT DELETE CONFIRM --}}
<script>
$('.delete-confirm').on('click', function(e) {
    e.preventDefault();
    let form = $(this).closest('form');

    Swal.fire({
        title: 'Hapus Article?',
        text: "Article ini akan dihapus dari sistem!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#0d6efd',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>

@endpush
