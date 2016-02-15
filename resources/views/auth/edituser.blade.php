@extends('layouts.app')

@section('content')
    
	<div class="uk-grid" data-uk-grid-margin="">
	<div class="app-sidebar uk-width-medium-1-4 uk-row-first">
		<ul class="app-nav uk-nav" data-uk-nav="">
			<li class="uk-nav-header">เมนูจัดการ</li>
			<li>
				<a href="{{ url('auth/listuser') }}">รายการผู้ใช้งานระบบ</a>
			</li>
			<li>
				<a href="{{ url('auth/register') }}">เพิ่มผู้ใช้งานระบบ</a>
			</li>
		</ul>
	</div>
	<div class="app-main uk-width-medium-3-4">

		<ul class="uk-breadcrumb">
		    <li><a href="{{ url('auth/listuser') }}">ผู้ใช้งานระบบ</a></li>
		    <li><span>รายการผู้ใช้งานระบบ</span></li>
		    <li class="uk-active"><span>แก้ไขผู้ใช้งานระบบ</span></li>
		</ul>

		<article class="uk-article">
				<h4 class="uk-article-title">แก้ไขผู้ใช้งานระบบ</h4>

				@if(Session::has('message'))
				 <div class="uk-alert uk-alert-danger" data-uk-alert="">
				 	<a class="uk-alert-close uk-close" href=""></a>
				 	<p>{{ Session::get('message') }}</p>
				 </div>
				@endif

		    	<form class="uk-form uk-form-horizontal" role="form" method="POST" action="{{ url('auth/updateuser') }}">
						{!! csrf_field() !!}
						<input type="hidden" value="{!! $data->id !!}" name="userid">
				    <fieldset>
				        <div class="uk-form-row">
							<label class="uk-form-label" for="fullname">ชื่อ-นามสกุล</label>
							<div class="uk-form-controls">
								<input type="text" placeholder="ชื่อ-นามสกุล" id="fullname" name="name" value="{!! $data->name !!}" >
								@if ($errors->has('name'))
		                            <span class="uk-text-danger">{{ $errors->first('name') }}</span>
		                        @endif
							</div>
						</div>
				        <div class="uk-form-row">
							<label class="uk-form-label" for="regis-username">ชื่อผู้ใช้งาน</label>
							<div class="uk-form-controls">
								<input type="text" placeholder="ชื่อผู้ใช้งาน" id="regis-username" name="username" value="{!! $data->username !!}" disabled>
								@if ($errors->has('username'))
		                            <span class="uk-text-danger">{{ $errors->first('username') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="regis-oldpassword">รหัสผ่านใหม่</label>
							<div class="uk-form-controls">
								<input type="password" placeholder="รหัสผ่านใหม่" id="regis-oldpassword" name="password">
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="regis-newpassword">ยืนยันรหัสผ่านใหม่</label>
							<div class="uk-form-controls">
								<input type="password" placeholder="ยืนยันรหัสผ่านใหม่" id="regis-newpassword" name="password_confirmation">
							</div>
						</div>
						<div class="uk-form-row">
							<div class="uk-form-controls">
								<input name="type" type="radio" value="user" id="typeuser" <?php echo (($data->type == 'user')?'checked="checked"':''); ?> ><label for="typeuser"> : ผู้ใช้งานทั่วไป</label><br>
								<input name="type" type="radio" value="admin" id="typeadmin" <?php echo (($data->type == 'admin')?'checked="checked"':''); ?> ><label for="typeadmin"> : ผู้ดูแลระบบ</label>
							</div>
						</div>
						<div class="uk-form-row">
							<div class="uk-form-controls">
								<button class="uk-button uk-button-primary">ตกลง</button>
								<a class="uk-button uk-button" href="{{ url('auth/listuser') }}">กลับ</a>
							</div>
						</div>
				    </fieldset>
				</form>

		</article>
	</div>
	</div>
    
		
		


@endsection