@extends('layouts.app')

@section('content')
    
    <div class="uk-grid" data-uk-grid-margin="">
    	<div class="app-sidebar uk-width-medium-1-4 uk-row-first">
			<ul class="app-nav uk-nav" data-uk-nav="">
				<!--<li class="uk-nav-header">เมนูจัดการ</li>
				<li>
					<a href="{{ url('routes/hotspot/userprofile') }}/{{ Crypt::encrypt($data->mtid) }}">รายการรูปแบบผู้ใช้งาน</a>
				</li>
				<li>
					<a href="{{ url('routes/hotspot/adduserprofile') }}/{{ Crypt::encrypt($data->mtid) }}">เพิ่มรูปแบบผู้ใช้งาน</a>
				</li>
				<li>
					<a href="{{ route( 'routes.show', Crypt::encrypt($data->mtid) ) }}">กลับหน้าสถานะอุปรกณ์</a>
				</li>-->

				@include('routes.menu-routes')	
			</ul>
    	</div>
		<div class="app-main uk-width-medium-3-4">

			<ul class="uk-breadcrumb uk-hidden-small">
			    <li><a href="{{ url('routes') }}">อุปกรณ์เชื่อมต่อ</a></li>
			    <li><span>{{ $data->mtname }}</span></li>
			    <li class="uk-active"><span>รูปแบบผู้ใช้งาน</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">รายการรูปแบบผู้ใช้งาน</h4>

					@if(Session::has('message'))
					 <div class="uk-alert uk-alert-danger" data-uk-alert="">
					 	<a class="uk-alert-close uk-close" href=""></a>
					 	<p>{{ Session::get('message') }}</p>
					 </div>
					@endif

					@if( isset($profile) )
						@if( count($profile) > 0 )
							<div class="uk-overflow-container">
							<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
								<thead>
									<tr>
										<th>ชื่อรูปแบบ</th>
										<th>เวลาการเชื่อมต่อใช้งานต่อครั้ง</th>
										<th>ผู้ใช้งานสามารถใช้ได้กี่เครื่อง</th>
										<th>การใช้งาน(อัพโหลด/ดาวน์โหลด)</th>
										<th>จัดการ</th>
									</tr>
								</thead>
								<tbody>
									@foreach($profile as $value)
				                        <tr>
				                            <td>{!! $value['name'] !!}</td>
											<td>{!! ((isset($value['session-timeout']))?$value['session-timeout']:'-') !!}</td>
				                            <td>{!! ((isset($value['shared-users']))?$value['shared-users']:'-') !!}</td>
				                            <td>{!! ((isset($value['rate-limit']))?$value['rate-limit']:'-') !!}</td>			                            
				                            <td>   
				                                <a class="uk-button uk-button-success uk-button-mini" href="{{ url('routes/hotspot/edituserprofile') }}/{{ Crypt::encrypt($value['name']) }}/{{ Crypt::encrypt($data->mtid) }}">
				                                    <i class="uk-icon-edit"></i> แก้ไข
				                                </a>
					                            <a class="uk-button uk-button-danger uk-button-mini" href="{{ url('routes/hotspot/deleteuserprofile') }}/{{ Crypt::encrypt($value['.id']) }}/{{ Crypt::encrypt($data->mtid) }}">
				                                    <i class="uk-icon-remove"></i> ลบ
				                                </a>                               
				                            </td>
				                        </tr>
				                    @endforeach
								</tbody>
							</table>
						</div>
						@endif	
					@endif

			</article>
		</div>
    </div>

@endsection