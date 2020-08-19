@extends('adminlte::page')

@section('title', $title)

@section('content_header')
	<h2>{{$title}}</h2>
@stop

@section('content')
	<div class="row">
		<div class="col-md-12">
			@if($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card card-success">
				<div class="card-header"><h3><i class="fas fa-file-import"></i> {{ $title }}</h3></div>
				<div class="card-body">
					{!! Form::open(['route' => 'set_import_series', 'method' => 'POST']) !!}
						<div class="form-group">
							<div class="input-group">
								<input type="text" placeholder="Url From Scrapper Series" @if(@$url) value='{{ @$url }}' @endif name="url" class="form-control" />
								<div class="input-group-append">
									<button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
								</div>
							</div>
						</div>
					{!! Form::close() !!}
					

					@if(!empty(@$data))
						{!! Form::open(['route' => 'saveSeries', 'method' => 'POST']) !!}

							<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save Series</button>

							<div class="form-group">
								<label>Title</label>
								<input type="text" readonly="readonly" name="title" class="form-control" value="{{ @$data->title }}" />
							</div>
							<div class="form-group">
								<label>Description</label>
								<textarea class="form-control" readonly="readonly" name="description">{{ @$data->description }}</textarea>
							</div>
							<div class="form-group">
								<label>Poster</label>
								<input type="hidden" name="poster" class="form-control" value="{{ @$data->poster }}" />
								@if(!empty(@$data->poster))
									<br />
									<img src="{{ $data->poster }}" class="img-thumbnail" style="width: 150px; height: 150px;">
								@endif
							</div>
							<div class="form-group">
								<label>Year</label>
								<input type="text" name="year" readonly="readonly" class="form-control" value="{{ @$data->year }}" />
							</div>
							<div class="form-group">
								<label>Categories</label>
								<select class="form-control multiple_select" style="width: 100%;" name="categories[]" multiple="multiple">
									@foreach(@$data->categories as $category)
										<option value="{{ $category }}" selected="selected">{{ $category }}</option>
									@endforeach
								</select>
							</div>

							<ul class="nav nav-tabs">
								@foreach(@$data->seasons as $key => $season)
									<li class="nav-item">
										<a class="nav-link @if($key == 0) active @endif" data-toggle="tab" href="#season_{{ $key }}">{{ $season['title'] }}</a>
										<input type="hidden" name="season_title[]" value="{{ $season['title'] }}" />
										<input type="hidden" name="season_position[]" value="{{ ($key + 1) }}" />
									</li>
								@endforeach
							</ul>

							<div class="tab-content">
								@foreach(@$data->seasons as $key => $season)
									<div class="tab-pane container @if($key == 0) active @endif" id="season_{{ $key }}" style="padding-top:30px;">
										<table class="table table-bordered table-striped">
											<thead>
												<th>Title</th>
												<th>Api Code</th>
												<th>-</th>
											</thead>
											<tbody>
												@foreach($season['chapters'] as $k => $chapter)
													<tr>
														<td>
															{{ $chapter['title'] }}
															<input type="hidden" name="chapter_title_{{ $key }}[]" value="{{ $chapter['title'] }}" />
															<input type="hidden" name="chapter_position_{{ $key }}[]" value="{{ ($k+1) }}" />
														</td>
														<td>
															{{ $chapter['data']['api_code'] }}
															<input type="hidden" name="chapter_api_code_{{ $key }}[]" value="{{ $chapter['data']['api_code'] }}" />
														</td>
														<td><button title="Test Movie" type="button" class="btn btn-success test-movie" data-code="{{ $chapter['data']['api_code'] }}"><i class="fas fa-eye"></i></button></td>
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								@endforeach
							</div>
							

						{!! Form::close() !!}
					@endif
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
        <h4 class="modal-title">View Serie Chapter In Player</h4>
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
@section('plugins.Select2', true)
@section('js')
<script type="text/javascript">
	$(document).ready(function(){

		$("select.multiple_select").select2();

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