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

			<ul class="uk-breadcrumb">
			    <li><a href="{{ url('routes') }}">อุปกรณ์เชื่อมต่อ</a></li>
			    <li class="uk-active"><span>รายการอุปกรณ์เชื่อมต่อ</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">รายการอุปกรณ์เชื่อมต่อ</h4>

					<div class="uk-overflow-container">
						<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
							<thead>
								<tr>
									<th>#</th>
									@if (Auth::user()->type == 'admin')
										<th>ผู้ดูแล</th>    	
						        	@endif
									<th>ชื่ออุปกรณ์</th>
									<th>ไอพี</th>
									<!--<th>พอร์ต</th>
									<th>ชื่อผู้ใช้งาน</th>
									<th>รหัสผ่าน</th>-->
									<th>รายละเอียด</th>
									<th>จัดการ</th>
								</tr>
							</thead>
							<tbody>
								<?php $i=1; ?>
								@foreach($data as $value)
			                        <tr>
			                            <td>{{$data->perPage()*($data->currentPage()-1)+$i}}</td>
			                            @if (Auth::user()->type == 'admin')
											    <td>{!! $value->name !!}</td>	
							        	@endif
			                            <td>{!! $value->mtname !!}</td>
										<td>{!! $value->mtip !!}</td>
										<!--<td>{!! $value->mtport !!}</td>
										<td>{!! $value->mtusername !!}</td>
			                            <td>{!! Crypt::decrypt($value->mtpassword) !!}</td> -->
			                            <td>{!! $value->mtdetail !!}</td>			                            
			                            <td>   
			                            	<a class="uk-button uk-button-primary uk-button-mini" href="{{ route( 'routes.show', Crypt::encrypt($value->mtid) ) }}">
			                                    <i class="uk-icon-cog"></i> จัดการ
			                                </a>
			                                <a class="uk-button uk-button-success uk-button-mini" href="{{ route( 'routes.edit', Crypt::encrypt($value->mtid) ) }}">
			                                    <i class="uk-icon-edit"></i> แก้ไข
			                                </a>
				                            <a class="uk-button uk-button-danger uk-button-mini" href="#" onclick="UIkit.modal.confirm('คุณต้องการลบอุปกรณ์เชื่อมต่อ {{ $value->mtname }}', function(){ delroutes('{{ $value->mtid }}'); });">
			                                    <i class="uk-icon-remove"></i> ลบ
			                                </a>                               
			                            </td>
			                        </tr>
			                    <?php $i++; ?>
			                    @endforeach
							</tbody>
						</table>
					</div>
					<?php echo $data->render(); ?>

			</article>
		</div>
    </div>

@endsection