@extends('layouts.app')

@section('content')
    
    <div class="uk-grid" data-uk-grid-margin="">
    	<div class="app-sidebar uk-width-medium-1-4 uk-row-first">
			<ul class="app-nav uk-nav" data-uk-nav="">
				<!--<li class="uk-nav-header">เมนูจัดการ</li>
				<li>
					<a href="{{ url('routes/hotspot/addusernet') }}/{{ Crypt::encrypt($data->mtid) }}">เพิ่มผู้ใช้งานอินเตอร์เน็ต</a>
				</li>
				<li>
					<a href="{{ url('routes/hotspot/usernet') }}/{{ Crypt::encrypt($data->mtid) }}">กลับหน้ารายการผู้ใช้งานอินเตอร์เน็ต</a>
				</li>-->

				@include('routes.menu-routes')
			</ul>
    	</div>
		<div class="app-main uk-width-medium-3-4">

			<ul class="uk-breadcrumb uk-hidden-small">
			    <li><a href="{{ url('routes') }}">อุปกรณ์เชื่อมต่อ</a></li>
			    <li><span>{{ $data->mtname }} </span></li>
			    <li class="uk-active"><span>เพิ่มผู้ใช้งานอินเตอร์เน็ต</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">เพิ่มผู้ใช้งานอินเตอร์เน็ต</h4>

					<form class="uk-form uk-form-horizontal" role="form" method="POST" action="{{ url('routes/hotspot/addusernet') }}" >
						{!! csrf_field() !!}
						<input type="hidden" name="mtid" value="{{ $data->mtid }}"> 

						<div class="uk-form-row">
							<label class="uk-form-label">ห้อง</label>
							<div class="uk-form-controls">
								{!! Form::select('comment', ['none'=>'none'] + $room_list, null, ['class'=> '']) !!} <span class="uk-text-primary">* ควรเลือกรายการ</span>
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label">ผู้ให้บริการ</label>
							<div class="uk-form-controls">
								{!! Form::select('server', ['none'=>'none'] + $server_list, null, ['class'=> '']) !!} <span class="uk-text-primary">* ควรเลือกรายการ</span>
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="name">ชื่อผู้ใช้งาน</label>
							<div class="uk-form-controls">
								<input id="name" name="name" type="text" placeholder="ชื่อผู้ใช้งาน"> <span class="uk-text-primary">* ควรกรอกข้อมูล</span>
								@if ($errors->has('name'))
		                            <span class="uk-text-danger">{{ $errors->first('name') }}</span> 
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="password">รหัสผ่าน</label>
							<div class="uk-form-controls">
								<input id="password" name="password" type="password" placeholder="รหัสผ่าน"> <span class="uk-text-primary">* ควรกรอกข้อมูล</span>
								@if ($errors->has('password'))
		                            <span class="uk-text-danger">{{ $errors->first('password') }}</span> 
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="email">อีเมล์</label>
							<div class="uk-form-controls">
								<input id="email" name="email" type="text" placeholder="อีเมล์">
								@if ($errors->has('email'))
		                            <span class="uk-text-danger">{{ $errors->first('email') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label">รูปแบบผู้ใช้งาน</label>
							<div class="uk-form-controls">
								{!! Form::select('profile', ['none'=>'none'] + $profile_list, null, ['class'=> '']) !!} <span class="uk-text-primary">* ควรเลือกรายการ</span>
							</div>
						</div>
						
						<div class="uk-form-row">
							<div class="uk-form-controls">
								<button class="uk-button uk-button-primary">บันทึก</button>
							</div>
						</div>
					</form>

			</article>
		</div>
    </div>

@endsection