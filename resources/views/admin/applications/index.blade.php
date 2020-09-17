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
					<h3><i class="fas fa-tags"></i> {{ $title }}</h3>
				</div>
				<div class="card-body">
					<a href="{{ route('applications.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> New Application</a>
					<br />
					<br />
					<table class="table table-bordered table-striped">
						<thead>
							<th>ID</th>
							<th>Name</th>
							<th>About</th>
							<th>Version</th>
							<th>-</th>
						</thead>
						<tbody>
							@foreach($data as $application)
								<tr>
									<td>{{ $application->id }}</td>
									<td>{{ $application->name }}</td>
									<td>{{ $application->about }}</td>
									<td>{{ $application->version }}</td>
									<td>
										<a href="{{ route('applications.edit',[$application->id]) }}" class="btn btn-info"><i class="fas fa-edit"></i></a>
										{!! Form::open(['route' => ['applications.destroy',$application->id], 'method' => 'DELETE', 'style' => 'display:inline;', 'id' => 'delete_'.$application->id]) !!}
											<button data-id="{{ $application->id }}" class="delete btn btn-danger" type="button"><i class="fas fa-times"></i></button>
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

	$("body").on('click','.delete', function(){	

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