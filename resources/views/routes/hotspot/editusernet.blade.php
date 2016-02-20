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
			    <li><span>{{ $data->mtname }} </span></li>
			    <li class="uk-active"><span>แก้ไขผู้ใช้งานอินเตอร์เน็ต</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">แก้ไขผู้ใช้งานอินเตอร์เน็ต</h4>

					@foreach($user as $value)
					<?php
						$id = $value['.id']; 					
						$server = ((isset($value['server']))?$value['server']:'');
						$name = ((isset($value['name']))?$value['name']:'');
						$password = ((isset($value['password']))?$value['password']:'');
						$profile = ((isset($value['profile']))?$value['profile']:'');
						$email = ((isset($value['email']))?$value['email']:'');
						$comment = ((isset($value['comment']))?$value['comment']:'');
					?>
					@endforeach

					<form class="uk-form uk-form-horizontal" role="form" method="POST" action="{{ url('routes/hotspot/editusernet') }}" >
						{!! csrf_field() !!}
						<input type="hidden" name="id" value="{{ $id }}"> 
						<input type="hidden" name="mtid" value="{{ $data->mtid }}"> 

						<div class="uk-form-row">
							<label class="uk-form-label">ห้อง</label>
							<div class="uk-form-controls">
								{!! Form::select('comment', ['none'=>'none'] + $room_list, $comment, ['class'=> '']) !!} <span class="uk-text-primary">* ควรเลือกรายการ</span>
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label">ผู้ให้บริการ</label>
							<div class="uk-form-controls">
								{!! Form::select('server', ['none'=>'none'] + $server_list, $server, ['class'=> '']) !!} <span class="uk-text-primary">* ควรเลือกรายการ</span>
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="name">ชื่อผู้ใช้งาน</label>
							<div class="uk-form-controls">
								<input id="name" name="name" type="text" placeholder="ชื่อผู้ใช้งาน" value="{{ $name }}"> <span class="uk-text-primary">* ควรกรอกข้อมูล</span>
								@if ($errors->has('name'))
		                            <span class="uk-text-danger">{{ $errors->first('name') }}</span> 
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="password">รหัสผ่าน</label>
							<div class="uk-form-controls">
								<input id="password" name="password" type="password" placeholder="รหัสผ่าน" value="{{ $password }}" > <span class="uk-text-primary">* ควรกรอกข้อมูล</span>
								@if ($errors->has('password'))
		                            <span class="uk-text-danger">{{ $errors->first('password') }}</span> 
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="email">อีเมล์</label>
							<div class="uk-form-controls">
								<input id="email" name="email" type="text" placeholder="อีเมล์" value="{{ $email }}">
								@if ($errors->has('email'))
		                            <span class="uk-text-danger">{{ $errors->first('email') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label">รูปแบบผู้ใช้งาน</label>
							<div class="uk-form-controls">
								{!! Form::select('profile', ['none'=>'none'] + $profile_list, $profile, ['class'=> '']) !!} <span class="uk-text-primary">* ควรเลือกรายการ</span>
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