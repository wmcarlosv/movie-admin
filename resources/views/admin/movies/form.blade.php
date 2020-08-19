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
				<h3><i class="fas fa-ticket-alt"></i> {{ $title }}</h3>
			</div>
			@if($type == 'new')
				{!! Form::open(['route' => 'movies.store', 'method' => 'POST', 'autocomplete' => 'off', 'files' => true]) !!}
			@else
				{!! Form::open(['route' => ['movies.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off', 'files' => true]) !!}
			@endif
				<div class="card-body">
					<div class="form-group">
						<label>Title:</label>
						<input type="text" name="title" class="form-control" value="{{ @$data->title }}" />
					</div>
					<div class="form-group">
						<label>Description:</label>
						<textarea name="description" class="form-control">{{ @$data->description }}</textarea>
					</div>
					<div class="form-group">
						<label>Categories:</label>
						<select class="form-control select-multiple" id="categories" name="categories[]" multiple="multiple">
							<option value="">-</option>
							@foreach($categories as $category)
								<option value="{{ $category->id }}">{{ $category->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>Year:</label>
						<input type="text" name="year" class="form-control" value="{{ @$data->year }}" />
					</div>
					<div class="form-group">
						<label>Poster:</label>
						<input type="file" name="poster" class="form-control" />
						@if(!empty(@$data->poster))
							<img src="{{ asset('storage/movies/'.@$data->poster) }}" class="img-thumbnail" style="width: 200px; height: 200px; margin-top: 15px;">
						@endif
					</div>
					<div class="form-group">
						<label>Api Code:</label>
						<input type="text" name="api_code" class="form-control" value="{{ @$data->api_code }}" />
					</div>
				</div>
				<div class="card-footer text-right">
					@if($type == 'edit')
					<a class="btn btn-info test-movie" href="#" data-code="{{ @$data->api_code }}"><i class="fas fa-eye"></i></a>
					@endif
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('movies.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
				</div>
			{!! Form::close() !!}
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
@section('plugins.Select2', true)
@section('js')
<script type="text/javascript">
	$(document).ready(function(){
		$("select.select-multiple").select2();

		@if($type == 'edit')
			$("select.select-multiple").val([@foreach($data->categories as $category)'{{ $category->id }}',@endforeach]).trigger('change');
		@endif

		$(".test-movie").click(function(){
			let api_code = $(this).attr('data-code');

			$.ajax({
			    url: 'https://feurl.com/api/source/'+api_code,
			    data: {},
			    type: 'POST',
			    crossDomain: true,
			    success: function(data) { 
			    	let files = data.data;
			    	let html = "";
			    	$("#qlf").empty();
			    	$.each(files, function(index, value){
			    		html+="<button class='btn btn-info set-player' data-url='"+value.file+"' type='button'>"+value.label+"</button>";
			    	});
			    	$("#qlf").html(html);
			    	html = "";
			    },
			    error: function(data) { console.log(data); }
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
	});
	
</script>
@stop