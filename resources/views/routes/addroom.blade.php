@extends('layouts.app')

@section('content')
    
    <div class="uk-grid" data-uk-grid-margin="">
    	<div class="app-sidebar uk-width-medium-1-4 uk-row-first">
			<ul class="app-nav uk-nav" data-uk-nav="">
				<!--<li class="uk-nav-header">เมนูจัดการ</li>
				<li>
					<a href="{{ url('routes/pageroom') }}/{{ Crypt::encrypt($data->mtid) }}">เพิ่มห้อง</a>
				</li>
				<li>
					<a href="{{ route( 'routes.show', Crypt::encrypt($data->mtid) ) }}">กลับหน้าสถานะอุปรกณ์</a>
				</li>-->

				@include('routes.menu-routes')
			</ul>
    	</div>
		<div class="app-main uk-width-medium-3-4">

			<ul class="uk-breadcrumb uk-hidden-small">
			    <li><a href="{{ url('routes') }}">อุปกรณ์เชื่อมต่อ</a></li>
			    <li><span>{{ $data->mtname }}</span></li>
			    <li class="uk-active"><span>เพิ่มห้อง</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">เพิ่มห้อง</h4>

					@if(Session::has('message'))
					 <div class="uk-alert uk-alert-danger" data-uk-alert="">
					 	<a class="uk-alert-close uk-close" href=""></a>
					 	<p>{{ Session::get('message') }}</p>
					 </div>
					@endif

					@if( isset($editroom) )
						<form class="uk-form uk-form-horizontal" role="form" method="POST" action="{{ url('routes/editroom') }}" >
							<input type="hidden" name="id" value="{{ $editroom->id }}"> 
					@else	
						<form class="uk-form uk-form-horizontal" role="form" method="POST" action="{{ url('routes/addroom') }}" >
					@endif
					
						{!! csrf_field() !!}
						<input type="hidden" name="mtid" value="{{ $data->mtid }}"> 

						<div class="uk-form-row">
							<label class="uk-form-label" for="room">ชื่อห้อง</label>
							<div class="uk-form-controls">
								@if( isset($editroom) )
									<input id="room" name="room" type="text" placeholder="ชื่อห้อง" value="{{ $editroom->room }}"> <span class="uk-text-primary">* ควรกรอกข้อมูล (A-Z, a-z, 0-9)</span>
								@else
									<input id="room" name="room" type="text" placeholder="ชื่อห้อง"> <span class="uk-text-primary">* ควรกรอกข้อมูล (A-Z, a-z, 0-9)</span>
								@endif
								
								@if ($errors->has('room'))
		                            <span class="uk-text-danger">{{ $errors->first('room') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="roomdetail">รายละเอียด</label>
							<div class="uk-form-controls">
								@if( isset($editroom) )
									<textarea id="roomdetail" name="roomdetail" placeholder="รายละเอียดห้อง" rows="5" cols="30">{{ $editroom->roomdetail }}</textarea>
								@else
									<textarea id="roomdetail" name="roomdetail" placeholder="รายละเอียดห้อง" rows="5" cols="30"></textarea>
								@endif
								
							</div>
						</div>
						<div class="uk-form-row">
							<div class="uk-form-controls">
								<button class="uk-button uk-button-primary">บันทึก</button>
							</div>
						</div>
					</form>

					<hr>

					@if( isset($room) )
						@if( count($room) > 0 )
							<div class="uk-overflow-container">
							<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
								<thead>
									<tr>
										<th>#</th>
										<th>ชื่อห้อง</th>
										<th>รายละเอียด</th>
										<th>จัดการ</th>
									</tr>
								</thead>
								<tbody>
									<?php $i=1; ?>
									@foreach($room as $value)
				                        <tr>
				                            <td>{{$room->perPage()*($room->currentPage()-1)+$i}}</td>
											<td>{!! $value->room !!}</td>
				                            <td>{!! $value->roomdetail !!}</td>			                            
				                            <td>   
				                                <a href="{{ url('routes/editpageroom') }}/{{ Crypt::encrypt($value['id']) }}/{{ Crypt::encrypt($value['mtid']) }}" data-uk-tooltip="{pos:'top'}" title="แก้ไข">
				                                    <i class="uk-icon-edit"></i>
				                                </a>
					                            <a href="{{ url('routes/deleteroom') }}/{{ Crypt::encrypt($value['mtid']) }}/{{ Crypt::encrypt($value['id']) }}" data-uk-tooltip="{pos:'top'}" title="ลบ">
				                                    <i class="uk-icon-remove"></i>
				                                </a>                               
				                            </td>
				                        </tr>
				                    <?php $i++;  ?>
				                    @endforeach
								</tbody>
							</table>
						</div>
						<?php echo $room->render(); ?>
						@endif	
					@endif

			</article>
		</div>
    </div>

@endsection