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
				{!! Form::open(['route' => 'series.store', 'method' => 'POST', 'autocomplete' => 'off', 'files' => true]) !!}
			@else
				{!! Form::open(['route' => ['series.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off', 'files' => true]) !!}
			@endif
				<div class="card-body">
					<div class="form-group">
						<label>Title:</label>
						<input type="text" name="title" class="form-control" value="{{ @$data->title }}" />
					</div>
					<div class="form-group">
						<label>Categories:</label>
						<select class="form-control select-multiple" name="categories[]" multiple="multiple">
							@foreach($categories as $category)
								<option value="{{ $category->id }}">{{ $category->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>Year:</label>
						<input type="text" name="year" class="form-control" value="{{ @$data->year }}" />
					</div>
					<div class="form-group">
						<label>Description:</label>
						<textarea name="description" class="form-control">{{ @$data->description }}</textarea>
					</div>
					<div class="form-group">
						<label>Poster:</label>
						<input type="file" name="poster" class="form-control" />
						@if(!empty(@$data->poster))
						<br />
						<img src="{{ asset('storage/series/'.@$data->poster) }}" class="img-thumbnail" style="width: 150px; height: 150px;" />
						@endif
					</div>
				</div>
				<div class="card-footer text-right">
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('series.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@stop

@section('plugins.Select2', true)
@section('js')
<script type="text/javascript">
	$(document).ready(function(){
		$('.select-multiple').select2();

		@if($type == 'edit')
			$('.select-multiple').val([@foreach(@$data->categories as $category)'{{ $category->id }}',@endforeach]).trigger('change');
		@endif
	});
</script>
@stop