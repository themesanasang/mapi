@extends('layouts.app')

@section('content')
    
    <div class="uk-grid" data-uk-grid-margin="">
		<div class="uk-width-medium-1-1">
			<article class="uk-article">
				<h2 class="uk-article-title">ข้อมูลสรุป</h2>

					@if( isset($server) )
						<div class="app-box-content">
							<div class="tm-grid-truncate uk-grid uk-grid-divider" data-uk-grid-margin="">
								<div class="uk-width-medium-1-3 uk-row-first">
									<div class="uk-panel">
										<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> การทำงานของ CPU Server</h3>
										<p class="uk-text-success">{{ $server['cpuload'] . " %" }}</p>
									</div>
								</div>
								<div class="uk-width-medium-1-3">
									<div class="uk-panel">
										<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> หน่วยความจำ Server ว่าง</h3>
										<p class="uk-text-success">{{ $server['memory_usage']  . " %" }}</p>
									</div>
								</div>
								<div class="uk-width-medium-1-3">
									<div class="uk-panel">
										<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> พื้นที่เก็บข้อมูล Server ว่าง</h3>
										<p class="uk-text-success">{{ $server['hdd_perc'] . " %" }}</p>
									</div>
								</div>
							</div>
					    </div>
						<hr>
					@endif

					<div class="app-box-content">
						<div class="tm-grid-truncate uk-grid uk-grid-divider" data-uk-grid-margin="">
							<div class="uk-width-medium-1-3 uk-row-first">
								<div class="uk-panel">
									<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> จำนวนผู้ใช้งานระบบ</h3>
									<p class="uk-text-success">ผู้ใช้งาน {{ $user }} คน</p>
								</div>
							</div>
							<div class="uk-width-medium-1-3">
								<div class="uk-panel">
									<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> จำนวนอุปกรณ์เชื่อมต่อ</h3>
									<p class="uk-text-success">อุปกรณ์เชื่อมต่อ {{ $mt }} ตัว</p>
								</div>
							</div>
							<div class="uk-width-medium-1-3">
								<div class="uk-panel">
									<h3 class="uk-panel-title"><i class="uk-icon-caret-right"></i> จำนวนบัตรที่มีการสร้าง</h3>
									<p class="uk-text-success">จำนวน {{ $card }} บัตร</p>
								</div>
							</div>
						</div>
				    </div>
					<hr>
					
					<br>
					<div class="app-box-content">
						<div id="chart01" style="height:300px;"></div>
					</div>


					
			</article>
		</div>
    </div>

@endsection