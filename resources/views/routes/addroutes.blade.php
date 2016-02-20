@extends('layouts.app')

@section('content')
    
    <div class="uk-grid" data-uk-grid-margin="">
    	<div class="app-sidebar uk-width-medium-1-4 uk-row-first">
			<ul class="app-nav uk-nav" data-uk-nav="">
				<li class="uk-nav-header">เมนูจัดการ</li>
				<li>
					<a href="{{ url('routes') }}">รายการอุปกรณ์เชื่อมต่อ</a>
				</li>
				<li>
					<a href="{{ url('routes/create') }}">เพิ่มอุปกรณ์เชื่อมต่อ</a>
				</li>
			</ul>
    	</div>
		<div class="app-main uk-width-medium-3-4">

			<ul class="uk-breadcrumb uk-hidden-small">
			    <li><a href="{{ url('routes') }}">อุปกรณ์เชื่อมต่อ</a></li>
			    <li class="uk-active"><span>เพิ่มอุปกรณ์เชื่อมต่อ</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">เพิ่มอุปกรณ์เชื่อมต่อ</h4>

					<form class="uk-form uk-form-horizontal" role="form" method="POST" action="{{ url('routes') }}" >
						{!! csrf_field() !!}

						@if (Auth::user()->type == 'admin')
							<div class="uk-form-row">
								<label class="uk-form-label" for="usermanage">ผู้ดูแลอุปกรณ์เชื่อมต่อ</label>
								<div class="uk-form-controls">
									{!! Form::select('usermanage', $userall, null, ['class'=> '']) !!}	
								</div>
							</div>       	
			        	@else
							<input type="hidden" name="usermanage" value="<?php echo Auth::user()->id; ?>">   
			        	@endif
						

						<div class="uk-form-row">
							<label class="uk-form-label" for="mtname">ชื่ออุปกรณ์</label>
							<div class="uk-form-controls">
								<input id="mtname" name="mtname" type="text" placeholder="ชื่ออุปกรณ์">
								@if ($errors->has('mtname'))
		                            <span class="uk-text-danger">{{ $errors->first('mtname') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="mtip">ไอพี</label>
							<div class="uk-form-controls">
								<input id="mtip" name="mtip" type="text" placeholder="ตัวอย่างไอพี 192.168.8.9">
								@if ($errors->has('mtip'))
		                            <span class="uk-text-danger">{{ $errors->first('mtip') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="mtport">พอร์ต</label>
							<div class="uk-form-controls">
								<input id="mtport" name="mtport" type="text" placeholder="ตัวอย่างพอร์ต 8728">
								@if ($errors->has('mtport'))
		                            <span class="uk-text-danger">{{ $errors->first('mtport') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="mtusername">ชื่อผู้ใช้งาน</label>
							<div class="uk-form-controls">
								<input id="mtusername" name="mtusername" type="text" placeholder="ชื่อผู้ใช้งานอุปกรณ์">
								@if ($errors->has('mtusername'))
		                            <span class="uk-text-danger">{{ $errors->first('mtusername') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="mtpassword">รหัสผ่าน</label>
							<div class="uk-form-controls">
								<input id="mtpassword" name="mtpassword" type="password" placeholder="รหัสผ่านอุปกรณ์">
								@if ($errors->has('mtpassword'))
		                            <span class="uk-text-danger">{{ $errors->first('mtpassword') }}</span>
		                        @endif
							</div>
						</div>
						<div class="uk-form-row">
							<label class="uk-form-label" for="mtdetail">รายละเอียด</label>
							<div class="uk-form-controls">
								<textarea id="mtdetail" name="mtdetail" placeholder="รายละเอียดอุปกรณ์เชื่อมต่อ" rows="5" cols="30"></textarea>
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