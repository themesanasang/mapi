@extends('layouts.app')

@section('content')
    
    <div class="uk-panel uk-panel-box uk-panel-box-primary uk-width-medium-1-2 uk-container-center" >
    <div class="app-box">

    		
		@if(Session::has('message'))
		 <div class="uk-alert uk-alert-danger" data-uk-alert="">
		 	<a class="uk-alert-close uk-close" href=""></a>
		 	<p>{{ Session::get('message') }}</p>
		 </div>
		@endif

		<form class="uk-form" role="form" method="POST" action="{{ url('auth/login') }}">
				{!! csrf_field() !!}
		    <fieldset>
		        <legend>เข้าสู่ระบบ</legend>
				<div class="uk-form-row">
					<label class="uk-form-label" for="username">ชื่อผู้ใช้งาน</label>
					<div class="uk-form-controls">
						<input type="text" placeholder="ชื่อผู้ใช้งาน" id="username" name="username" class="uk-width-1-1">
						@if ($errors->has('username'))
                            <span class="uk-text-danger">{{ $errors->first('username') }}</span>
                        @endif
					</div>
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label" for="password">รหัสผ่าน</label>
					<div class="uk-form-controls">
						<input type="password" placeholder="รหัสผ่าน" id="password" name="password" class="uk-width-1-1">
						@if ($errors->has('password'))
                            <span class="uk-text-danger">{{ $errors->first('password') }}</span>
                        @endif
					</div>
				</div>
				<div class="uk-form-row">
					<button class="uk-button uk-button-primary uk-button-large uk-width-1-1 uk-margin-small-bottom">เข้าสู่ระบบ</button>
				</div>
		    </fieldset>
		</form>
    				

	</div>
    </div>

    <div class="uk-container uk-container-center uk-text-center uk-margin-top">
    	<p>2016 © ThemeSanasang. </p>
    </div>

@endsection