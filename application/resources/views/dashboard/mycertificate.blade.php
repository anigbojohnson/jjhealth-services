@extends('welcome')
@section('title',"My Certificates")
@section('content')
<div class="dashboard" >
@include('dashboard.sidebar')
<div class="main-content">
<div class="table-header">

    <div class="header-top">

        <div class="header-title">
            <h2>Medical Certificates</h2>
        </div>

   <div class="header-action">
        <a href="{{ route('certificate') }}" class="btn-primary">
            + Request New Certificate
        </a>
    </div>

    </div>

    <p class="header-description">
        View the status of your requests, download approved certificates,
        or submit a new request.
    </p>
</div>

    <table class="certificate-table">
        <thead>
            <tr>
                <th>Request Date</th>
                <th>Certificate</th>
                <th>Valid From</th>
                <th>Valid To</th>
                <th>Status</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
            @foreach($certificates as $certificate)
            <tr>
                <td>{{   \Carbon\Carbon::parse($certificate->requestDate)->format('d M Y') }}</td>
                <td>{{ $certificate->seeking }}</td>
                <td>{{ $certificate->validFrom }}</td>
                <td>{{ $certificate->validTo }}</td>
                <td>
                    @if($certificate->request_status == "approved")
                        <span class="badge success">
                            Approved
                        </span>
                    @elseif($certificate->request_status == "declined")
                        <span class="badge danger">
                            Declined
                        </span>
                    @else
                        <span class="badge warning">
                            Pending
                        </span>
                    @endif
                </td>
                <td>
                    @if($certificate->request_status == "approved")
                        <a href="{{ route('download', ['fileName' => $certificate->fileUpload]) }}" 
                           class="download-btn">
                            ⬇ Download
                        </a>
                    @else
                        —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination">
    @if($certificates->onFirstPage())
        <span class="disabled">Previous</span>
    @else
        <a href="{{ $certificates->previousPageUrl() }}">
            ← Previous
        </a>
    @endif
    @foreach($certificates->getUrlRange(1, $certificates->lastPage()) as $page => $url)
        @if($page == $certificates->currentPage())
            <span class="active">{{ $page }}</span>
        @else
            <a href="{{ $url }}">{{ $page }}</a>
        @endif
    @endforeach

    @if($certificates->hasMorePages())
        <a href="{{ $certificates->nextPageUrl() }}">
            Next →
        </a>
    @else
        <span class="disabled">Next</span>
    @endif

      </div>
    </div>
</div>
<style>
    .main-content{flex:1;background:#fff;padding:20px;}
    .dashboard{display:flex;min-height:80vh;}
    .pagination{display:flex;justify-content:center;align-items:center;gap:10px;margin-top:30px;}
    .pagination a,
    .pagination span{min-width:42px;height:42px;display:flex;align-items:center;justify-content:center;padding:0 16px;border-radius:10px;text-decoration:none;font-weight:600;border:1px solid #ddd;}
    .pagination a{background:white;color:#2E8B57;transition:.25s;}
    .pagination a:hover{background:#2E8B57;color:white;}
    .pagination .active{background:#2E8B57;color:white;border-color:#2E8B57;}
    .pagination .disabled{background:#f5f5f5;color:#999;cursor:not-allowed;}
    .certificate-table{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);margin-top:30px;}
    table{width:100%;border-collapse:collapse;}
    thead{background:#2E8B57;color:#fff;}
    thead th{padding:18px 20px;text-align:left;font-size:14px;font-weight:600;letter-spacing:.5px;}
    tbody tr{border-bottom:1px solid #ececec;transition:background .2s ease;}
    tbody tr:hover{background:#f7fbf8;}
    tbody td{padding:18px 20px;color:#444;font-size:15px;vertical-align:middle;}
    tbody tr:last-child{border-bottom:none;}
    .badge{display:inline-block;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;}
    .success{background:#dcfce7;color:#166534;}
    .warning{background:#fef3c7;color:#92400e;}
    .danger{background:#fee2e2;color:#991b1b;}
    .download-btn{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;background:#2E8B57;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;transition:.2s;}
    .download-btn:hover{background:#256f46;}
    .table-header{background:#F1FBF5;border:1px solid #D8F0E0;border-radius:16px;padding:24px 30px;margin-bottom:25px;}
    .header-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;}
    .header-title h2{margin:0;font-size:32px;font-weight:700;color:#1f2937;}
    .header-description{margin:0 0 20px 0;max-width:650px;color:#6b7280;line-height:1.6;}
    .header-action{display:flex;justify-content:flex-end;}
    .btn-primary{display:inline-block;padding:12px 22px;background:#2E8B57;color:#fff;text-decoration:none;border-radius:10px;font-weight:600;transition:.2s;}
    .btn-primary:hover{background:#256f46;}

</style>
@endsection
