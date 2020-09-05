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
						<input type="text" name="name" required="required" class="form-control" value="{{ @$data->name }}" />
					</div>
					<div class="form-group">
						<label>Email:</label>
						<input type="email" name="email" required="required" class="form-control" value="{{ @$data->email }}" />
					</div>
					<div class="form-group">
						<label>Role</label>
						<select class="form-control" required="required" name="role">
							<option value=''>-</option>
							<option value='admin' @if(@$data->role == 'admin') selected='selected' @endif>Admin</option>
							<option value='operator' @if(@$data->role == 'operator') selected='selected' @endif>Operator</option>
							<option value='client' @if(@$data->role == 'client') selected='selected' @endif>Client</option>
						</select>
					</div>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text bg-info">CLIENT ID:</span>
						</div>
						<input type="text" placeholder="Client ID" readonly="readonly" value="{{ @$data->client_id }}" name="client_id" class="form-control" />
						<div class="input-group-append">
							<button class="btn btn-success" type="button" id="generate_client_id"><i class="fas fa-key"></i></button>
						</div>
					</div>
					<div class="form-group">
						<label>Password:</label>
						<input type="text" name="password" @if($type == 'new') required="required" @endif class="form-control"/>
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

@section('js')
<script type="text/javascript">
	function makeid(length) {
	   var result           = '';
	   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	   var charactersLength = characters.length;
	   for ( var i = 0; i < length; i++ ) {
	      result += characters.charAt(Math.floor(Math.random() * charactersLength));
	   }
	   return result;
	}

	$(document).ready(function(){

		@if($type == 'new')
			let client_id = makeid(5);
			$("input[name='client_id']").val(client_id);
		@endif


		$("#generate_client_id").click(function(){
			let client_id = makeid(5);
			$("input[name='client_id']").val(client_id);
		});
	});
</script>
@stop