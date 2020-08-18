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
				<h3><i class="fas fa-play"></i> {{ $title }}</h3>
			</div>
			@if($type == 'new')
				{!! Form::open(['route' => 'seasons.store', 'method' => 'POST', 'autocomplete' => 'off']) !!}
			@else
				{!! Form::open(['route' => ['seasons.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off']) !!}
			@endif
				<div class="card-body">
					<div class="form-group">
						<label>Serie:</label>
						<select class="form-control select-simple" name="serie_id">
							<option value="">-</option>
							@foreach($series as $serie)
								<option value="{{ $serie->id }}" @if(@$data->serie_id == $serie->id) selected='selected' @endif>{{ $serie->title }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>Title</label>
						<input type="text" name="title" class="form-control" value="{{ @$data->title}}">
					</div>
					<div class="form-group">
						<label>Position</label>
						<input type="text" name="position" class="form-control" value="{{ @$data->position}}">
					</div>
				</div>
				<div class="card-footer text-right">
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('seasons.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@stop
@section('plugins.Select2', true);
@section('js')
<script type="text/javascript">
	$(document).ready(function(){
		$('select.select-simple').select2();
	});
</script>
@stop