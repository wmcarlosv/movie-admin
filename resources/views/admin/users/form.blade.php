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
				<h3><i class="fas fa-user"></i> {{ $title }}</h3>
			</div>
			@if($type == 'new')
				{!! Form::open(['route' => 'users.store', 'method' => 'POST', 'autocomplete' => 'off']) !!}
			@else
				{!! Form::open(['route' => ['users.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off']) !!}
			@endif
				<div class="card-body">
					<div class="form-group">
						<label>Name:</label>
						<input type="text" name="name" class="form-control" value="{{ @$data->name }}" />
					</div>
					<div class="form-group">
						<label>Email:</label>
						<input type="email" name="email" class="form-control" value="{{ @$data->email }}" />
					</div>
					<div class="form-group">
						<label>Role</label>
						<select class="form-control" name="role">
							<option value=''>-</option>
							<option value='admin' @if(@$data->role == 'admin') selected='selected' @endif>Admin</option>
							<option value='operator' @if(@$data->role == 'operator') selected='selected' @endif>Operator</option>
							<option value='client' @if(@$data->role == 'client') selected='selected' @endif>Client</option>
						</select>
					</div>
					<div class="form-group">
						<label>Password:</label>
						<input type="text" name="password" class="form-control"/>
					</div>
				</div>
				<div class="card-footer text-right">
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('users.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@stop