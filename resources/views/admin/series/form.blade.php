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
				<h3><i class="fas fa-tv"></i> {{ $title }}</h3>
			</div>
			@if($type == 'new')
				{!! Form::open(['route' => 'series.store', 'method' => 'POST', 'autocomplete' => 'off', 'files' => true]) !!}
			@else
				{!! Form::open(['route' => ['series.update',$data->id], 'method' => 'PUT', 'autocomplete' => 'off', 'files' => true]) !!}
			@endif
				<div class="card-body">
					<div class="form-group">
						<label>Title:</label>
						<input type="text" name="title" class="form-control" value="{{ @$data->title }}" />
					</div>
					<div class="form-group">
						<label>Categories:</label>
						<select class="form-control select-multiple" name="categories[]" multiple="multiple">
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
						<label>Description:</label>
						<textarea name="description" class="form-control">{{ @$data->description }}</textarea>
					</div>
					<div class="form-group">
						<label>Poster:</label>
						<input type="file" name="poster" class="form-control" />
						@if(!empty(@$data->poster))
						<br />
						<img src="{{ asset('storage/series/'.@$data->poster) }}" class="img-thumbnail" style="width: 150px; height: 150px;" />
						@endif
					</div>
					@if($type == 'edit')
						<ul class="nav nav-tabs">
							@foreach(@$data->seasons as $key => $season)
								<li class="nav-item">
									<a class="nav-link @if($key == 0) active @endif" data-toggle="tab" href="#season_{{ $season->id }}">{{ $season->title }}</a>
								</li>
							@endforeach
						</ul>

						<div class="tab-content">
							@foreach(@$data->seasons as $key => $season)
								<div class="tab-pane container @if($key == 0) active @endif" style="padding-top:30px;">
									<table class="table table-bordered table-striped">
										<thead>
											<th>Title</th>
											<th>View</th>
										</thead>
										<tbody>
											@foreach($season->chapters as $chapter)
												<tr>
													<td>{{ $chapter->title }}</td>
													<td>
														<a class="btn btn-info view-chapter" data-api-code="{{ $chapter->api_code }}" href="#"><i class="fas fa-eye"></i></a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							@endforeach
						</div>
					@endif
				</div>
				<div class="card-footer text-right">
					<button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save</button>
					<a href="{{ route('series.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
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

@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('js')
<script type="text/javascript">
	$(document).ready(function(){
		$('.select-multiple').select2();
		$('table').DataTable();
		@if($type == 'edit')
			$('.select-multiple').val([@foreach(@$data->categories as $category)'{{ $category->id }}',@endforeach]).trigger('change');
		@endif

		$('body').on('click','a.view-chapter', function(){

			let api_code = $(this).attr('data-api-code');

			$.ajax({
			    url: 'https://feurl.com/api/source/'+api_code,
			    type: 'POST',
			    dataType: "jsonp",
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