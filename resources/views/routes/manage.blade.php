@extends('layouts.app')

@section('content')
    
    <div class="uk-grid" data-uk-grid-margin="">
    	<div class="app-sidebar uk-width-medium-1-4 uk-row-first">
			<ul class="app-nav uk-nav" data-uk-nav="">
				<li class="uk-nav-header">เมนูจัดการ</li>
				<li>
					<a href="{{ url('routes/pageroom') }}/{{ Crypt::encrypt($data->mtid) }}">เพิ่มห้อง</a>
				</li>
				<li>
					<a href="{{ url('routes') }}">กลับหน้ารายการ</a>
				</li>
			</ul>
    	</div>
		<div class="app-main uk-width-medium-3-4">
			<article class="uk-article">
					<h4 class="uk-article-title">สถานะอุปกรณ์เชื่อมต่อ {{ $data->mtname }}</h4>

					<div class="tm-grid-truncate uk-grid uk-grid-divider" data-uk-grid-margin="">
						<div class="uk-width-medium-1-3 uk-row-first">
							<div class="uk-panel">
								<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> การทำงานของ CPU</h3>
								<p class="uk-text-success">{{ $first['cpu-load'] . " %" }}</p>
							</div>
						</div>
						<div class="uk-width-medium-1-3">
							<div class="uk-panel">
								<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> หน่วยความจำว่าง</h3>
								<p class="uk-text-success">{{ number_format($mem,3) . " %" }}</p>
							</div>
						</div>
						<div class="uk-width-medium-1-3">
							<div class="uk-panel">
								<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> พื้นที่เก็บข้อมูลว่าง</h3>
								<p class="uk-text-success">{{ number_format($hdd,3) . " %" }}</p>
							</div>
						</div>
					</div>
					<hr class="uk-grid-divider">
					<div class="uk-grid uk-grid-divider" data-uk-grid-margin="">
						<div class="uk-width-medium-1-3 uk-row-first">
							<div class="uk-panel">
								<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> อุปกรณ์เชื่อมต่อทำงานมาแล้ว</h3>
								<p class="uk-text-success">{{ $uptime }}</p>
							</div>
						</div>
						<div class="uk-width-medium-1-3">
							<div class="uk-panel">
								<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> กลุ่มผู้ใช้อินเตอร์เน็ตทั้งหมด</h3>
								<p class="uk-text-success">รอ...</p>
							</div>
						</div>
						<div class="uk-width-medium-1-3">
							<div class="uk-panel">
								<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> ผู้ใช้อินเตอร์เน็ต ณ ปัจจุบัน</h3>
								<p class="uk-text-success">{{ $useronline }} ผู้ใช้งาน</p>
							</div>
						</div>
					</div>

			</article>
		</div>
    </div>

@endsection