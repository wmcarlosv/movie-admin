@extends('adminlte::page')

@section('title', $title)

@section('content_header')
	<h2>{{$title}}</h2>
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		@if ($errors->any())
		    <div class="alert alert-danger">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif
		<div class="card card-success">
			<div class="card-header">
				<h3><i class="fas fa-tag"></i> {{ $title }}</h3>
			</div>
			@if($type == 'new')
				{!! Form::open(['route' => 'categories.store', 'method' => 'POST', 'autocomplete' => 'off']) !!}
			@else
				{!! Form::open(['route' => ['categories.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off']) !!}
			@endif
				<div class="card-body">
					<div class="form-group">
						<label>Name:</label>
						<input type="text" name="name" class="form-control" value="{{ @$data->name }}" />
					</div>
					<!--<div class="form-check">
					  <label class="form-check-label">
					    <input type="checkbox" name="is_for_channel" @if(@$data->is_for_channel == 'Y') checked='checked' @endif class="form-check-input" value="Y">Is For Channel
					  </label>-->
					</div>
				</div>
				<div class="card-footer text-right">
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('categories.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@stop