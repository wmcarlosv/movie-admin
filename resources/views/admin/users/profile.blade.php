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
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="card card-success">
			<div class="card-header">
				<h3><i class="fas fa-user"></i> Profile</h3>
			</div>
			{!! Form::open(['route' => 'update_profile', 'method' => 'POST', 'autocomplete' => 'off']) !!}
			<div class="card-body">
				<div class="form-group">
					<label>Name:</label>
					<input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" />
				</div>
				<div class="form-group">
					<label>Email:</label>
					<input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" />
				</div>
				<div class="form-group">
					<label>Role:</label>
					<input type="text" readonly="readonly" name="role" class="form-control" value="{{ Auth::user()->role }}" />
				</div>
				@if(Auth::user()->role == 'admin')
					<div class="form-group">
						<label>Import Current Page: </label>
						<input type="text" class="form-control" readonly="readonly" value="{{ $current_page }}" />
					</div>
				@endif
			</div>
			<div class="card-footer text-right">
				<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="card card-success">
			<div class="card-header">
				<h3><i class="fas fa-key"></i> Change Password</h3>
			</div>
			{!! Form::open(['route' => 'change_password', 'method' => 'POST']) !!}
			<div class="card-body">
				<div class="form-group">
					<label>Password: </label>
					<input type="password" name="password" class="form-control" />
				</div>
				<div class="form-group">
					<label>Repeat Password: </label>
					<input type="password" name="password_confirmation" class="form-control" />
				</div>
			</div>
			<div class="card-footer text-right">
				<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
			</div>
			{!! Form::close() !!}
		</div>		
	</div>
</div>
@stop

@section('plugins.Sweetalert2', true)
@section('js')
<script type="text/javascript">
	$(document).ready(function(){
		@if(Session::has('success'))

			Swal.fire({
				title: "{{ Session::get('success') }}",
				type: "success"
			});
			
		@endif

		@if(Session::has('error'))

			Swal.fire({
				title: "{{ Session::get('error') }}",
				type: "error"
			});
			
		@endif
	});
</script>
@stop