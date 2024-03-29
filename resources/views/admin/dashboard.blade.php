@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
	<h2>Dashboard</h2>
@stop

@section('css')
<style type="text/css">
	div#pagination-footer{
		margin-top: 15px;
		float: right;
	}
</style>
@stop

@section('content')
   	<div class="row">
		<div class="col-md-12">
			<div class="info-box">
		    	<span class="info-box-icon bg-red"><i class="fas fa-tags"></i></span>
		    	<div class="info-box-content">
		    		<span class="info-box-text">Categories</span>
		    		<span class="info-box-number">{{ $ccat }}</span>
		    	</div>
		    </div>
		</div>
		<div class="col-md-12">
			<div class="info-box">
		    	<span class="info-box-icon bg-blue"><i class="fas fa-ticket-alt"></i></span>
		    	<div class="info-box-content">
		    		<span class="info-box-text">Movies</span>
		    		<span class="info-box-number">{{ $cmov }}</span>
		    	</div>
		    </div>
		</div>
		<!--<div class="col-md-12">
			<div class="info-box">
		    	<span class="info-box-icon bg-green"><i class="fas fa-tv"></i></span>
		    	<div class="info-box-content">
		    		<span class="info-box-text">Series</span>
		    		<span class="info-box-number">{{ $cser }}</span>
		    	</div>
		    </div>
		</div>
		<div class="col-md-12">
			<div class="info-box">
		    	<span class="info-box-icon bg-yellow"><i class="fas fa-tv"></i></span>
		    	<div class="info-box-content">
		    		<span class="info-box-text">Channels</span>
		    		<span class="info-box-number">{{ $channels->count() }}</span>
		    	</div>
		    </div>
		</div>-->
	</div>
	<!--<div class="row">
		<div class="col-md-12">
			<div class="card card-success">
				<div class="card-header">
					<h3><i class="fas fa-ticket-alt"></i> List Movies</h3>
				</div>
				<div class="card-body">
					<div class="row" style="margin-bottom: 20px;">
						<div class="col-md-6">
							<div class="search">
								{!! Form::open(['route' => 'dashboard', 'method' => 'GET']) !!}
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
								{{ $movies_availables->appends(['q' => $q])->links() }}
							</div>
						</div>
					</div>
					
					
					<table class="table table-bordered table-striped">
						<thead>
							<th>Title</th>
							<th style="width: 25%;">Description</th>
							<th style="width: 15%;">Categories</th>
							<th>Year</th>
							<th>Poster</th>
							<th>-</th>
						</thead>
						<tbody>
							@foreach($movies_availables as $mva)
								<tr>
									<td>{{ $mva->title }}</td>
									<td style="font-size: 14px; text-align: justify;">{{ $mva->description }}</td>
									<td>
										@foreach($mva->categories as $cat)
											<span class="badge badge-success">{{ $cat->name }}</span>
										@endforeach
									</td>
									<td>{{ $mva->year }}</td>
									<td>
										<img src="{{ asset('storage/movies/'.$mva->poster) }}" class="img-thumbnail" style="width: 125px; height: 125px;" />
									</td>
									<td><a class="btn btn-info get-movie-data" data-toggle="modal" data-movie-code="{{ $mva->api_code }}" data-target="#movie_details" href="#"><i class="fas fa-eye"></i> View Links</a></td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div id="pagination-footer">
						{{ $movies_availables->appends(['q' => $q])->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
	

	<div class="modal" id="movie_details">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">
						Movie Links
					</h3>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-striped">
						<thead>
							<th>Link</th>
							<th>Quality</th>
						</thead>
						<tbody id="load_links">
							
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger" id="close-modal" type="button"><i class="fas fa-times"></i> Close</button>
				</div>
			</div>
		</div>
	</div>-->
@stop
@section('plugins.Datatables', true)
@section('js')
	<script type="text/javascript">
		$(document).ready(function(){
			$(".data-table").DataTable();

			$('body').on('click','.get-movie-data', function(){
				var mc = $(this).attr('data-movie-code');
				$("#load_links").append('<tr><td colspan="2" align="center">Cargando..</td></tr>');
				$.post("{{ route('getDataVideo') }}",{api_code : mc, _token: "{{ csrf_token() }}"}, function(data){
					let files = JSON.parse(data);
					var html = "";

					$.each(files.data, function(i, e){
						html+="<tr>";
							html+="<td><a target='_blank' href='{{ url('/') }}/see/{{ Auth::user()->client_id }}|"+mc+"|"+e.label+"'>{{ url('/') }}/see/{{ Auth::user()->client_id }}|"+mc+"|"+e.label+"</a></td>";
							html+="<td>"+e.label+"</td>";
						html+="<tr>";
					});

					$("#load_links").html(html);
					html = "";
				});
			});

			$("#close-modal").click(function(){
				$("#load_links").empty();
				$("#movie_details").modal('hide');
			});
		});
	</script>
@stop