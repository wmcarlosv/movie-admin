@extends('adminlte::page')

@section('title', $title)

@section('content_header')
	<h2>{{$title}}</h2>
@stop

@section('content')
	<div class="row">	
		<div class="col-md-12">
			<div class="card card-success">
				<div class="card-header">
					<h3><i class="fas fa-users"></i> {{ $title }}</h3>
				</div>
				<div class="card-body">
					<a href="{{ route('users.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> New User</a>
					<br />
					<br />
					<table class="table table-bordered table-striped">
						<thead>
							<th>ID</th>
							<th>Name</th>
							<th>Email</th>
							<th>Role</th>
							<th>Status</th>
							<th>-</th>
						</thead>
						<tbody>
							@foreach($data as $user)
								<tr>
									<td>{{ $user->id }}</td>
									<td>{{ $user->name }}</td>
									<td>{{ $user->email }}</td>
									<td>{{ $user->role }}</td>
									<td>
										@if($user->status == 'A')
											<span class="badge badge-success">Active</span>
										@else
											<span class="badge badge-danger">Inactive</span>
										@endif
									</td>
									<td>
										<a href="{{ route('users.edit',[$user->id]) }}" class="btn btn-info"><i class="fas fa-edit"></i></a>
										{!! Form::open(['route' => ['users.destroy',$user->id], 'method' => 'DELETE', 'style' => 'display:inline;', 'id' => 'delete_'.$user->id]) !!}
											<button data-id="{{ $user->id }}" class="delete btn @if($user->status == 'A') btn-danger @else btn-success @endif" type="button">@if($user->status == 'A') <i class="fas fa-times"></i> @else <i class="fas fa-plus"></i> @endif</button>
										{!! Form::close() !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@stop

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('js')
<script type="text/javascript">
	$("table.table").DataTable();

	$(".delete").click(function(e){	

		Swal.fire({
			title: "Are you sure to perform this action?",
			type: 'question',
			showCancelButton: true
		}).then((result) => {
			if(result.value){
				let id = $(this).attr("data-id");
				$("#delete_"+id).submit();
			}
		});
	});

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
</script>
@stop