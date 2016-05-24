@extends('layouts.app')

@section('content')
    
    <div class="uk-grid" data-uk-grid-margin="">
    	<div class="app-sidebar uk-width-medium-1-4 uk-row-first">
			<ul class="app-nav uk-nav" data-uk-nav="">
				@include('routes.menu-routes')
			</ul>
    	</div>
		<div class="app-main uk-width-medium-3-4">

			<ul class="uk-breadcrumb uk-hidden-small">
			    <li><a href="{{ url('routes') }}">อุปกรณ์เชื่อมต่อ</a></li>
			    <li><span>{{ $data->mtname }}</span></li>
			    <li class="uk-active"><span>ย้ายห้องผู้ใช้งานอินเตอร์เน็ต</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">ย้ายห้องผู้ใช้งานอินเตอร์เน็ต</h4>

					<div class="app-box-content">
					<div class="uk-grid">

						<input type="hidden" id="movemtid" value="{{ Crypt::encrypt($data->mtid) }}"> 

						<!-- left -->
					    <div class="uk-width-medium-4-10">
					    	<div class="uk-panel uk-panel-box">
					    		<form class="uk-form">
							    	<div class="uk-form-row">
										<label class="uk-form-label">เลือกห้อง:</label>
										<div class="uk-form-controls">
											{!! Form::select('oldcomment', ['none'=>'เลือก'] + $room_list, null, ['class'=> 'uk-width-medium-1-1', 'id'=>'old_roomlist']) !!} 										
										</div>
									</div>
								</form>
								<hr>
								<div id="showuserroom"></div>
					    	</div>
					    </div>

						<!-- center -->
					    <div id="movecontrol" class="uk-width-medium-2-10">
					    	<div class="uk-panel uk-panel-box">
								<form class="uk-form">
								<div class="uk-form-row">
									<label class="uk-form-label">ห้อง:</label>
									<div class="uk-form-controls">
										{!! Form::select('movecomment', ['none'=>'เลือก'] + $room_list, null, ['class'=> 'uk-width-medium-1-1', 'disabled'=>'', 'id'=>'move_roomlist']) !!} 										
									</div>
								</div>
					    		<!--<div class="uk-form-row">
									<label class="uk-form-label">ผู้ให้บริการ:</label>
									<div class="uk-form-controls">
										{!! Form::select('moveserver', ['none'=>'เลือก'] + $server_list, null, ['class'=> 'uk-width-medium-1-1', 'disabled'=>'', 'id'=>'move_serverlist']) !!}
									</div>
								</div>-->
								<div class="uk-form-row">
									<label class="uk-form-label">รูปแบบผู้ใช้งาน:</label>
									<div class="uk-form-controls">
										{!! Form::select('moveprofile', ['none'=>'เลือก'] + $profile_list, null, ['class'=> 'uk-width-medium-1-1', 'disabled'=>'', 'id'=>'move_profilelist']) !!}
									</div>
								</div>
								</form>
								<hr>
								<button id="moveroomclick" data-uk-tooltip="{pos:'top'}" title="ย้ายห้อง" class="uk-button uk-width-medium-1-1" type="button" disabled><i class="uk-icon-arrow-circle-right"></i></button>
					    	</div>
					    </div>

						<!-- right -->
					    <div id="moveresult" class="uk-width-medium-4-10">
					    	<div class="uk-panel uk-panel-box">
					    		<div id="h-room-new">ห้อง</div>
								<hr>
								<div id="showuserroom_new"></div>
					    	</div>
					    </div>

					</div>
					</div>

			</article>
		</div>
    </div>

@endsection