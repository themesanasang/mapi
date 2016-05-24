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
			    <li class="uk-active"><span>รายการผู้ใช้งานระบบ</span></li>
			</ul>

			<article class="uk-article">
					<h4 class="uk-article-title">รายการผู้ใช้งานระบบ</h4>

					<div class="uk-overflow-container">
						<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
							<thead>
								<tr>
									<th>#</th>
									<th>ชื่อ-นามสกุล</th>
									<th>ชื่อผู้ใช้งาน</th>
									<th>ระดับ</th>
									<th>จัดการ</th>
								</tr>
							</thead>
							<tbody>
								<?php $i=1; ?>
								@foreach($data as $value)
			                        <tr>
			                            <td>{{$data->perPage()*($data->currentPage()-1)+$i}}</td>
			                            <td>{!! $value['name'] !!}</td>
			                            <td>{!! $value['username'] !!}</td> 
			                            <td>{!! $value['type'] !!}</td>			                            
			                            <td>   
			                                 <a class="uk-button uk-button-success uk-button-mini" href="{{ url('auth/edituser') }}/{{ Crypt::encrypt($value['id']) }}">
			                                    <i class="uk-icon-edit"></i> แก้ไข
			                                </a>
				                                <a class="uk-button uk-button-danger uk-button-mini" href="#" onclick="UIkit.modal.confirm('คุณต้องการลบผู้ใช้งาน {{ $value['username'] }}', function(){ deluser('{{ $value['id'] }}'); });">
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