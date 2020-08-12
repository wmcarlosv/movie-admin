@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
	<h2>Dashboard</h2>
@stop

@section('content')
   	<div class="row">
		<div class="col-md-6">
			<div class="info-box">
		    	<span class="info-box-icon bg-red"><i class="fas fa-tags"></i></span>
		    	<div class="info-box-content">
		    		<span class="info-box-text">Categories</span>
		    		<span class="info-box-number">{{ $ccat }}</span>
		    	</div>
		    </div>
		</div>
		<div class="col-md-6">
			<div class="info-box">
		    	<span class="info-box-icon bg-blue"><i class="fas fa-ticket-alt"></i></span>
		    	<div class="info-box-content">
		    		<span class="info-box-text">Movies</span>
		    		<span class="info-box-number">{{ $cmov }}</span>
		    	</div>
		    </div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="card card-info">
				<div class="card-header">
					<h3><i class="fas fa-eye"></i> Most Views</h3>
				</div>
				<div class="card-body">
					<table class="table table-bordered table-striped">
						<thead>
							<th>ID</th>
							<th>Title</th>
							<th>Photo</th>
							<th>Views</th>
						</thead>
						<tbody>
							
						</tbody>
					</table>	
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card card-success">
				<div class="card-header">
					<h3><i class="fas fa-download"></i> Most Downloads</h3>
				</div>
				<div class="card-body">
					<table class="table table-bordered table-striped">
						<thead>
							<th>ID</th>
							<th>Title</th>
							<th>Photo</th>
							<th>Views</th>
						</thead>
						<tbody>
							
						</tbody>
					</table>	
				</div>
			</div>
		</div>
	</div>
@stop
@section('plugins.Datatables', true)
@section('js')
	<script type="text/javascript">
		$(document).ready(function(){
			$(".table").DataTable();
		});
	</script>
@stop