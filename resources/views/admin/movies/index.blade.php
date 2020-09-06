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
					<h3><i class="fas fa-ticket-alt"></i> {{ $title }}</h3>
				</div>
				<div class="card-body">
					<a href="{{ route('movies.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> New Movie</a>
					<br />
					<br />
					<div class="row" style="margin-bottom: 20px;">
						<div class="col-md-6">
							<div class="search">
								{!! Form::open(['route' => 'movies.index', 'method' => 'GET']) !!}
								<div class="input-group">
									<input type="text" name="q" value="{{ @$q }}" id="q" class="form-control" />
									<div class="input-group-append">
										<button class="btn btn-success"><i class="fas fa-search"></i></button>
									</div>
								</div>
								{!! Form::close() !!}
							</div>
						</div>
						<div class="col-md-6">
							<div id="pagination" style="float:right;">
								{{ $data->appends(['q' => $q])->links() }}
							</div>
						</div>
					</div>

					<table class="table table-bordered table-striped">
						<thead>
							<th>ID</th>
							<th>Title</th>
							<th>Poster</th>
							<th>Categories</th>
							<th>Year</th>
							<th>Views</th>
							<th>Downloads</th>
							<th>Status</th>
							<th>-</th>
						</thead>
						<tbody>
							@foreach($data as $movie)
								<tr>
									<td>{{ $movie->id }}</td>
									<td width="300">{{ $movie->title }}</td>
									<td>
										@if(isset($movie->poster))
											<img src="{{ asset('storage/movies/'.$movie->poster) }}" class="img-thumbnail" style="width: 150px; height: 150px;" />
										@else
											<span class="badge badge-info">Not Image</span>
										@endif
									</td>
									<td width="200">
										@foreach($movie->categories as $category)
											<span class="badge badge-info">{{ $category->name }}</span>
										@endforeach
									</td>
									<td>{{ $movie->year }}</td>
									<td>{{ $movie->views }}</td>
									<td>{{ $movie->downloads }}</td>
									<td>
										@if($movie->status == 'A')
											<span class="badge badge-success">Active</span>
										@else
											<span class="badge badge-danger">Inactive</span>
										@endif
									</td>
									<td>
										<a href="{{ route('movies.edit',[$movie->id]) }}" class="btn btn-info"><i class="fas fa-edit"></i></a>
										{!! Form::open(['route' => ['movies.destroy',$movie->id], 'method' => 'DELETE', 'style' => 'display:inline;', 'id' => 'delete_'.$movie->id]) !!}
											<button data-id="{{ $movie->id }}" class="delete btn @if($movie->status == 'A') btn-danger @else btn-success @endif" type="button">@if($movie->status == 'A') <i class="fas fa-times"></i> @else <i class="fas fa-plus"></i> @endif</button>
										{!! Form::close() !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div id="pagination" style="float:right; margin-top: 20px;">
						{{ $data->appends(['q' => $q])->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('js')
<script type="text/javascript">

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