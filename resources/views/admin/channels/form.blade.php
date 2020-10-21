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
				{!! Form::open(['route' => 'channels.store', 'method' => 'POST', 'autocomplete' => 'off', 'files' => true]) !!}
			@else
				{!! Form::open(['route' => ['channels.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off', 'files' => true]) !!}
			@endif
				<div class="card-body">
					<div class="form-group">
						<label>Title:</label>
						<input type="text" name="title" class="form-control" value="{{ @$data->title }}" />
					</div>
					<div class="form-group">
						<label>Description:</label>
						<textarea class="form-control" name="description">{{ @$data->description }}</textarea>
					</div>
					<div class="form-group">
						<label>Poster:</label>
						<input type="file" name="poster" class="form-control" />
						@if(!empty(@$data->poster))
							<img src="{{ asset('storage/channels/'.@$data->poster) }}" class="img-thumbnail" style="width: 200px; height: 200px; margin-top: 15px;">
						@endif
					</div>
					<div class="form-group">
						<label>Url:</label>
						<input type="text" name="url" class="form-control" value="{{ @$data->url }}" />
					</div>
				</div>
				<div class="card-footer text-right">
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('channels.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@stop