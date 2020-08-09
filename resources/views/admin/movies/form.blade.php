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
				<h3><i class="fas fa-ticket-alt"></i> {{ $title }}</h3>
			</div>
			@if($type == 'new')
				{!! Form::open(['route' => 'movies.store', 'method' => 'POST', 'autocomplete' => 'off', 'files' => true]) !!}
			@else
				{!! Form::open(['route' => ['movies.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off', 'files' => true]) !!}
			@endif
				<div class="card-body">
					<div class="form-group">
						<label>Title:</label>
						<input type="text" name="title" class="form-control" value="{{ @$data->title }}" />
					</div>
					<div class="form-group">
						<label>Description:</label>
						<textarea name="description" class="form-control">{{ @$data->description }}</textarea>
					</div>
					<div class="form-group">
						<label>Categories:</label>
						<select class="form-control select-multiple" id="categories" name="categories[]" multiple="multiple">
							<option value="">-</option>
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
						<label>Poster:</label>
						<input type="file" name="poster" class="form-control" />
						@if(!empty(@$data->poster))
							<img src="{{ asset('storage/movies/'.@$data->poster) }}" class="img-thumbnail" style="width: 200px; height: 200px; margin-top: 15px;">
						@endif
					</div>
					<div class="form-group">
						<label>Api Code:</label>
						<input type="text" name="api_code" class="form-control" value="{{ @$data->api_code }}" />
					</div>
				</div>
				<div class="card-footer text-right">
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('movies.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
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
		$("select.select-multiple").select2();

		@if($type == 'edit')
			$("select.select-multiple").val([@foreach($data->categories as $category)'{{ $category->id }}',@endforeach]).trigger('change');
		@endif
	});
	
</script>
@stop