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
			    <li class="uk-active"><span>อัพโหลดไฟล์ผู้ใช้งานอินเตอร์เน็ต</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">อัพโหลดไฟล์ผู้ใช้งานอินเตอร์เน็ต</h4>

					<div class="exp-excel">
						<a href="{{ url('storage/originalexcel/user_internet.xls') }}" class="uk-button uk-button-success">
							<i class="uk-icon-download"></i> ตัวอย่างไฟล์ Excel
						</a>
					</div>

					<hr>

					<div class="upload-excel">
						<form class="uk-form" action="{{ url('routes/hotspot/addfileusernet') }}" method="post" enctype="multipart/form-data">
							{!! csrf_field() !!}
							<input type="hidden" name="mtid" value="{{ $data->mtid }}"> 

							<input type="file" name="fileexcel">
							@if (Session::has("fileexcel"))
	                            <span class="uk-text-danger">{{ Session::get('fileexcel') }}</span> 
	                        @endif
						
							<button class="uk-button uk-button-primary" type="submit"><i class="uk-icon-upload"></i> อัพโหลดไฟล์</button>
						</form>
						@if (Session::has("fileexcelok"))
								<br>
	                            <span class="uk-text-danger">{{ Session::get('fileexcelok') }}</span> 
	                    @endif
					</div>

			</article>
		</div>
    </div>

@endsection