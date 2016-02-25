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

					<div class="uk-grid">

						<!-- left -->
					    <div class="uk-width-medium-4-10">
					    	<div class="uk-panel uk-panel-box">
					    		<form class="uk-form">
							    	<div class="uk-form-row">
										<label class="uk-form-label">เลือกห้อง:</label>
										<div class="uk-form-controls">
											{!! Form::select('comment', ['none'=>'เลือก'] + $room_list, null, ['class'=> 'uk-width-medium-1-1']) !!} 										
										</div>
									</div>
								</form>
								<hr>

					    	</div>
					    </div>

						<!-- center -->
					    <div id="movecontrol" class="uk-width-medium-2-10">
					    	<div class="uk-panel uk-panel-box">
								<form class="uk-form">
								<div class="uk-form-row">
									<label class="uk-form-label">ห้อง:</label>
									<div class="uk-form-controls">
										{!! Form::select('comment', ['none'=>'เลือก'] + $room_list, null, ['class'=> 'uk-width-medium-1-1']) !!} 										
									</div>
								</div>
					    		<div class="uk-form-row">
									<label class="uk-form-label">ผู้ให้บริการ:</label>
									<div class="uk-form-controls">
										{!! Form::select('server', ['none'=>'เลือก'] + $server_list, null, ['class'=> 'uk-width-medium-1-1']) !!}
									</div>
								</div>
								<div class="uk-form-row">
									<label class="uk-form-label">รูปแบบผู้ใช้งาน:</label>
									<div class="uk-form-controls">
										{!! Form::select('profile', ['none'=>'เลือก'] + $profile_list, null, ['class'=> 'uk-width-medium-1-1']) !!}
									</div>
								</div>
								</form>
								<hr>
					    		<a class="uk-button uk-width-medium-1-1" href="#" data-uk-tooltip="{pos:'top'}" title="ย้ายห้อง"> <i class="uk-icon-arrow-circle-right"></i> </a>
					    	</div>
					    </div>

						<!-- right -->
					    <div id="moveresult" class="uk-width-medium-4-10">
					    	<div class="uk-panel uk-panel-box">
					    		<div id="">ห้อง</div>
								<hr>

					    	</div>
					    </div>

					</div>

			</article>
		</div>
    </div>

@endsection