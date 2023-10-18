@extends('admin.layouts.app')

@section('style')

@endsection

@section('topbar')
@parent
@endsection

@section('sidebar')
@parent
@endsection

@section('page-title','Show Brand')

@section('content')
<a href="{{ url('/admin/portfolio-images') }}" class="btn btn-primary">Go To Back</a>
<hr>

<table class="table">
    <tbody>
        <tr>
            <div class="mb-3">
                <img src="{{ asset('/storage/' . $portfolioImage->image) }}" alt="image" width="200px" height="200px">
            </div>
        </tr>
        <tr>
            <td>Image Label  :</td>
            <td>{{ $portfolioImage->image_label }}</td>
        </tr>
        <tr>
            <td>Image Description   :</td>
            <td>{{ $portfolioImage->image_description }}</td>
        </tr>
    </tbody>
</table>
@endsection

@section('script')
@endsection