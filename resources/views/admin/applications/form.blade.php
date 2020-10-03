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
				{!! Form::open(['route' => 'applications.store', 'method' => 'POST', 'autocomplete' => 'off']) !!}
			@else
				{!! Form::open(['route' => ['applications.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off']) !!}
			@endif
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Name:</label>
								<input type="text" required="required" name="name" class="form-control" value="{{ @$data->name }}" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Version:</label>
								<input type="text" required="required" name="version" class="form-control" value="{{ @$data->version }}" />
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label>About:</label>
						<textarea class="form-control" required="required" name="about">{{ @$data->about }}</textarea>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Play Store Url:</label>
								<input type="text" name="play_store_url" class="form-control" value="{{ @$data->play_store_url }}" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>App Access Code:</label>
								<input type="text" maxlength="10" required="required" minlength="10" name="app_code" class="form-control" value="{{ @$data->app_code }}" />
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label>Privacy Policy:</label>
						<textarea class="form-control" name="privacy_policy">{{ @$data->privacy_policy }}</textarea>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Url App Qualify:</label>
								<input type="text" name="url_qualify" class="form-control" value="{{ @$data->url_qualify }}" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Url App More Apps:</label>
								<input type="text" name="url_more_apps" class="form-control" value="{{ @$data->url_more_apps }}" />
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('applications.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@stop