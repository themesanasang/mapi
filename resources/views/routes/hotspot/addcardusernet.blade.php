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
			    <li class="uk-active"><span>สร้างบัตร์ผู้ใช้งานอินเตอร์เน็ต</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">สร้างบัตร์ผู้ใช้งานอินเตอร์เน็ต</h4>

					<div class="upload-excel">
						<form class="uk-form uk-form-horizontal" action="{{ url('routes/hotspot/addcardusernet') }}" method="post">
							{!! csrf_field() !!}
							<input type="hidden" name="mtid" value="{{ $data->mtid }}"> 

							<div class="uk-form-row">
								<label class="uk-form-label">ห้อง</label>
								<div class="uk-form-controls">
									{!! Form::select('comment', ['none'=>'เลือก'] + $room_list, null, ['class'=> '']) !!} <span class="uk-text-primary">* ควรเลือกรายการ</span>
									@if (Session::has("comment"))
			                            <span class="uk-text-danger">{{ Session::get('comment') }}</span> 
			                        @endif
								</div>
							</div>
							<!--<div class="uk-form-row">
								<label class="uk-form-label">ผู้ให้บริการ</label>
								<div class="uk-form-controls">
									{!! Form::select('server', ['none'=>'เลือก'] + $server_list, null, ['class'=> '']) !!} <span class="uk-text-primary">* ควรเลือกรายการ</span>
									@if (Session::has("server"))
			                            <span class="uk-text-danger">{{ Session::get('server') }}</span> 
			                        @endif
								</div>
							</div>-->
							<div class="uk-form-row">
								<label class="uk-form-label">รูปแบบผู้ใช้งาน</label>
								<div class="uk-form-controls">
									{!! Form::select('profile', ['none'=>'เลือก'] + $profile_list, null, ['class'=> '']) !!} <span class="uk-text-primary">* ควรเลือกรายการ</span>
									@if (Session::has("profile"))
			                            <span class="uk-text-danger">{{ Session::get('profile') }}</span> 
			                        @endif
								</div>
							</div>
							<div class="uk-form-row">
								<label class="uk-form-label" for="cardvalue">จำนวนบัตร</label>
								<div class="uk-form-controls">
									<input type="text" name="cardvalue" placeholder="จำนวนบัตร" > <span class="uk-text-primary">* ควรกรอกข้อมูล</span>
									@if ($errors->has('cardvalue'))
			                            <span class="uk-text-danger">{{ $errors->first('cardvalue') }}</span> 
			                        @endif
		                    	</div>
							</div>
							<div class="uk-form-row">
								<div class="uk-form-controls">
									<button class="uk-button uk-button-primary" type="submit">สร้างบัตร</button>
								</div>
							</div>								
						</form>
					</div>

			</article>
		</div>
    </div>

@endsection