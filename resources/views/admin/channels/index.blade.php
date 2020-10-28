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
					<a href="{{ route('channels.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> New Channel</a>
					<br />
					<br />
					<table class="table table-bordered table-striped">
						<thead>
							<th>ID</th>
							<th>Title</th>
							<th>Category</th>
							<th>Description</th>
							<th>Poster</th>
							<th>-</th>
						</thead>
						<tbody>
							@foreach($data as $channel)
								<tr>
									<td>{{ $channel->id }}</td>
									<td>{{ $channel->title }}</td>
									<td>
										@if(isset($channel->category_id) and !empty($channel->category_id))
											{{ $channel->category->name }}
										@else
											No Category
										@endif
									</td>
									<td>{{ $channel->description }}</td>
									<td>
									@if(isset($channel->poster))
											<img src="{{ asset('storage/channels/'.$channel->poster) }}" class="img-thumbnail" style="width: 150px; height: 150px;" />
										@else
											<span class="badge badge-info">Not Image</span>
										@endif
									</td>
									<td>
										<a href="{{ route('channels.edit',[$channel->id]) }}" class="btn btn-info"><i class="fas fa-edit"></i></a>
										{!! Form::open(['route' => ['channels.destroy',$channel->id], 'method' => 'DELETE', 'style' => 'display:inline;', 'id' => 'delete_'.$channel->id]) !!}
											<button data-id="{{ $channel->id }}" class="delete btn @if($channel->status == 'A') btn-danger @else btn-success @endif" type="button">@if($channel->status == 'A') <i class="fas fa-times"></i> @else <i class="fas fa-plus"></i> @endif</button>
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