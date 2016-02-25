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
			    <li class="uk-active"><span>ผู้ใช้งานอินเตอร์เน็ต</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">รายการผู้ใช้งานอินเตอร์เน็ต</h4>

					@if( isset($user) )
						@if( count($user) > 0 )
							<div class="app-search">
								<form class="uk-form form-search">
									<input type="hidden" value="{{ Crypt::encrypt($data->mtid) }}" id="mtid" >
									<p id="userdelbt">
										<a class="uk-button uk-button-danger " id="ckdelall">ลบ</a>
									</p>
									<div class="form-search-box">
										ค้นหาห้อง : {!! Form::select('comment', ['All'=>'ทั้งหมด'] + $room_list, null, ['id'=> 'searchuser']) !!}
									</div>
								</form>
							</div>
							<br>
							<div id="tableuser">
								<div id="usermanage">
								<div class="uk-overflow-container">
									<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
										<thead>
											<tr>
												<th><input name="user_select_all" value="1" id="user-select-all" type="checkbox"></th>
												<th>ห้อง</th>
												<th>ผู้ให้บริการ</th>
												<th>ชื่อผู้ใช้งาน</th>
												<th>รูปแบบผู้ใช้งาน</th>
												<th>อีเมล์</th>
												<th>จัดการ</th>
											</tr>
										</thead>
										<tbody>
											<input type="hidden" name="mtid" value="{{ $data->mtid }}">
											@foreach($user as $value)
						                        <tr>
						                        	<td><input type="checkbox" name="id[]" value="{{ $value['.id'] }}"></td>
						                        	<td>{!! ((isset($value['comment']))?$value['comment']:'-') !!}</td>
						                            <td>{!! ((isset($value['server']))?$value['server']:'-') !!}</td>
						                            <td>{!! ((isset($value['name']))?$value['name']:'-') !!}</td>
						                            <td>{!! ((isset($value['profile']))?$value['profile']:'-') !!}</td>
						                            <td>{!! ((isset($value['email']))?$value['email']:'-') !!}</td>		                            
						                            <td>   
						                                <a href="{{ url('routes/hotspot/editusernet') }}/{{ Crypt::encrypt($value['name']) }}/{{ Crypt::encrypt($data->mtid) }}" data-uk-tooltip="{pos:'top'}" title="แก้ไข">
						                                    <i class="uk-icon-edit"></i>
						                                </a>
							                            <a href="{{ url('routes/hotspot/deleteusernet') }}/{{ Crypt::encrypt($value['.id']) }}/{{ Crypt::encrypt($data->mtid) }}" data-uk-tooltip="{pos:'top'}" title="ลบ">
						                                    <i class="uk-icon-remove"></i>
						                                </a>                               
						                            </td>
						                        </tr>
						                    @endforeach
										</tbody>
									</table>

									<?php echo $user->render(); ?>

								</div>
								</div>
							</div>
						@endif	
					@endif

			</article>
		</div>
    </div>

@endsection