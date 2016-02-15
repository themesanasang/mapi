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
			<article class="uk-article">
				<div class="uk-alert uk-alert-danger">
					<h4 class="uk-article-title">อุปกรณ์เชื่อมต่อ  #{{ $data->mtname }} อยู่ในสถานะ ไม่พร้อมทำงาน โปรดตรวจสอบ  </h4>
				</div>
			</article>
		</div>
    </div>

@endsection