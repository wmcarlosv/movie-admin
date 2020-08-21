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
				<h3><i class="fas fa-file-import"></i> {{ $title }}</h3>
			</div>	
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						{!! Form::open(['route' => 'set_import_movies', 'method' => 'POST']) !!}
						<div class="row">
							<div class="col-md-6">
								<div class="input-group">
									<input type="text" name="url" value="{{ $q }}" placeholder="Url For Search Movie, This Format ==> https://pelisplushd.net/peliculas?page=" class="form-control">
									<div class="input-group-append">
										<button type="submit" id="search-movies" class="btn btn-success"><i class="fas fa-search"></i></button>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-check">
									<div class="form-check-label">
										<input type="checkbox" name="search_type" @if(!empty($ts)) checked='checked' @endif class="form-check-input"> Is Single Search
									</div>
								</div>
							</div>
						</div>
						{!! Form::close() !!}
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-12">
						@if(count($data) > 0)
							<button class="btn btn-success" id="save_movies" type="button"><i class="fas fa-save"></i> Save Movies</button>
							<br />
							<br />
						@endif
						<table class="table table-bordered table-striped">
							<thead>
								<th>Title</th>
								<th>Year</th>
								<th>Poster</th>
								<th>Categories</th>
								<th>Code Api</th>
								<th>-</th>
							</thead>
							<tbody>
								{!! Form::open(['route' => 'save_movies', 'method' => 'POST', 'id' => 'form_movies']) !!}
								@foreach($data as $imovie)
									<tr>
										<td>
											<input type="hidden" name="titles[]" value="{{ $imovie['title'] }}"/>
											<input type="hidden" name="descriptions[]" value="{{ $imovie['description'] }}"/>
										{{ $imovie['title'] }}</td>
										<td><input type="hidden" name="years[]" value="{{ $imovie['year'] }}"/>{{ $imovie['year'] }}</td>
										<td>
											<input type="hidden" name="posters[]" value="{{ $imovie['poster'] }}"/>
											<img src="{{ $imovie['poster'] }}" style="width: 100px; height: 100px" />
										</td>
										<td><input type="hidden" name="categories[]" value="{{ $imovie['categories'] }}"/>{{ $imovie['categories'] }}</td>
										<td><input type="hidden" name="api_codes[]" value="{{ $imovie['api_code'] }}"/>{{ $imovie['api_code'] }}</td>
										<td><button title="Test Movie" type="button" class="btn btn-success test-movie" data-code="{{ $imovie['api_code'] }}"><i class="fas fa-eye"></i></button></td>
									</tr>
								@endforeach
								{!! Form::close() !!}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- The Modal -->
<div class="modal" id="movie-view-modal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">View Movie In Player</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
      	<h3>Quality</h3>
      	<hr />
        <center><div class="btn-group" id="qlf"></div></center>
        <hr />
        <div id="player"></div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" id="close-modal" class="btn btn-danger">Close</button>
      </div>

    </div>
  </div>
</div>
@stop
@section('plugins.Sweetalert2', true)
@section('js')
<script type="text/javascript">
	$(document).ready(function(){

		$(".test-movie").click(function(){
			let api_code = $(this).attr('data-code');

			$.post("{{ route('getDataVideo') }}", { api_code: api_code, _token: "{{ csrf_token() }}" }, function(data){
				let files = JSON.parse(data);
				
		    	let html = "";
		    	$("#qlf").empty();
		    	$.each(files.data, function(index, value){
		    		html+="<button class='btn btn-info set-player' data-url='"+value.file+"' type='button'>"+value.label+"</button>";
		    	});
		    	$("#qlf").html(html);
		    	html = "";
			});

			$('#movie-view-modal').modal('show');
		});

		$("body").on('click','button.set-player', function(){
			let url_player = $(this).attr("data-url");
			$("#player").empty();
			$("#player").html("<video width='100%' height='240' controls><source src='"+url_player+"' type='video/mp4'/></video>");
		});

		$("#close-modal").click(function(){
			$("#player").empty();
			$('#movie-view-modal').modal('hide');
		});

		$("#save_movies").click(function(){
			$("#form_movies").submit();
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

	});
</script>
@stop