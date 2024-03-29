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
					<h3><i class="fas fa-tv"></i> {{ $title }}</h3>
				</div>
				<div class="card-body">
					<a href="{{ route('series.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> New Serie</a>
					<br />
					<br />
					<table class="table table-bordered table-striped">
						<thead>
							<th>ID</th>
							<th>Title</th>
							<th>Year</th>
							<th>Poster</th>
							<th>-</th>
						</thead>
						<tbody>
							@foreach($data as $serie)
								<tr>
									<td>{{ $serie->id }}</td>
									<td>{{ $serie->title }}</td>
									<td>{{ $serie->year }}</td>
									<td>
										@if(!empty($serie->poster))
											<img class="img-thumbnail" src="{{ asset('storage/series/'.$serie->poster) }}" style="width: 150px; height: 150px;"></img>
										@else
											<span class="badge badge-warning">Not Poster</span>
										@endif
									</td>
									<td>
										<a href="{{ route('series.edit',[$serie->id]) }}" class="btn btn-info"><i class="fas fa-edit"></i></a>
										{!! Form::open(['route' => ['series.destroy',$serie->id], 'method' => 'DELETE', 'style' => 'display:inline;', 'id' => 'delete_'.$serie->id]) !!}
											<button data-id="{{ $serie->id }}" class="delete btn btn-danger" type="button"><i class="fas fa-times"></i></button>
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