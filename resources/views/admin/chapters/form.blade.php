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
				<h3><i class="fas fa-video"></i> {{ $title }}</h3>
			</div>
			@if($type == 'new')
				{!! Form::open(['route' => 'chapters.store', 'method' => 'POST', 'autocomplete' => 'off']) !!}
			@else
				{!! Form::open(['route' => ['chapters.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off']) !!}
			@endif
				<div class="card-body">
					<div class="form-group">
						<label>Serie:</label>
						<select class="form-control simple-select" id="serie_id" name="serie_id">
							<option value="">-</option>
							@foreach($series as $serie)
								<option value="{{ $serie->id }}" @if(@$data->season->serie->id == $serie->id) selected='selected' @endif>{{ $serie->title }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>Season:</label>
						<select class="form-control" id="season_id" name="season_id">
							<option value="">-</option>
							@if($type == 'edit')
								@foreach($seasons as $season)
									<option value="{{ $season->id }}" @if($season->id == @$data->season_id) selected='selected' @endif>{{ $season->title }}</option>
								@endforeach
							@endif
						</select>
					</div>
					<div class="form-group">
						<label>Title</label>
						<input type="text" name="title" class="form-control" value="{{ @$data->title }}" />
					</div>
					<div class="form-group">
						<label>Position</label>
						<input type="text" name="position" class="form-control" value="{{ @$data->position }}" />
					</div>
					<div class="form-group">
						<label>Api Code</label>
						<input type="text" name="api_code" class="form-control" value="{{ @$data->api_code }}" />
					</div>
				</div>
				<div class="card-footer text-right">
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('chapters.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
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
		$("select.simple-select").select2();

		$("#serie_id").change(function(){
			let id = $(this).val();

			$("#season_id").empty();
			if(id){
				$.get('/admin/chapters/'+id, function(data){
					$.each(data, function(e,v){
						$("#season_id").append('<option value="'+v.id+'">'+v.title+'</option>');
					});
				});
			}else{
				$("#season_id").empty();
			}
			
		});
	});
</script>
@stop