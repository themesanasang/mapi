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
		    <li class="uk-active"><span>เพิ่มผู้ใช้งานระบบ</span></li>
		</ul>
			
		<article class="uk-article">
				<h4 class="uk-article-title">เพิ่มผู้ใช้งานระบบ</h4>

				@if(Session::has('message'))
				 <div class="uk-alert uk-alert-danger" data-uk-alert="">
				 	<a class="uk-alert-close uk-close" href=""></a>
				 	<p>{{ Session::get('message') }}</p>
				 </div>
				@endif

		    	<form class="uk-form uk-form-horizontal" role="form" method="POST" action="{{ url('auth/register') }}">
					{!! csrf_field() !!}
				    <fieldset>
				        <div class="uk-form-row">
							<label class="uk-form-label" for="fullname">ชื่อ-นามสกุล</label>
							<div class="uk-form-controls">
								<input type="text" placeholder="ชื่อ-นามสกุล" id="fullname" name="name" >
								@if ($errors->has('name'))
		                            <span class="uk-text-danger">{{ $errors->first('name') }}</span>
		                        @endif
							</div>
						</div>
				        <div class="uk-form-row">
							<label class="uk-form-label" for="regis-username">ชื่อผู้ใช้งาน</label>
							<div class="uk-form-controls">
								<input type="text" placeholder="ชื่อผู้ใช้งาน" id="regis-username" name="username">
								@if ($errors->has('username'))
		                            <span class="uk-text-danger">{{ $errors->first('username') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="regis-password">รหัสผ่าน</label>
							<div class="uk-form-controls">
								<input type="text" placeholder="รหัสผ่าน" id="regis-password" name="password">
								@if ($errors->has('password'))
		                            <span class="uk-text-danger">{{ $errors->first('password') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<div class="uk-form-controls">
								<input name="type" type="radio" value="user" id="typeuser" checked="checked"><label for="typeuser"> : ผู้ใช้งานทั่วไป</label><br>
								<input name="type" type="radio" value="admin" id="typeadmin" ><label for="typeadmin"> : ผู้ดูแลระบบ</label>
							</div>
						</div>
						<div class="uk-form-row">
							<div class="uk-form-controls">
								<button class="uk-button uk-button-primary">ตกลง</button>
							</div>
						</div>
				    </fieldset>
				</form>

		</article>
	</div>
	</div>
    
		
		


@endsection