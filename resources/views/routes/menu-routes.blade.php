
<li class="uk-nav-header">สถานะอุปรกณ์</li>
<li>
	<a href="{{ route( 'routes.show', Crypt::encrypt($data->mtid) ) }}">สถานะอุปรกณ์</a>
</li>

<li class="uk-nav-header">จัดการห้อง</li>
<li>
	<a href="{{ url('routes/pageroom') }}/{{ Crypt::encrypt($data->mtid) }}">เพิ่มห้อง</a>
</li>

<li class="uk-nav-header">รูปแบบผู้ใช้งาน</li>
<li>
	<a href="{{ url('routes/hotspot/userprofile') }}/{{ Crypt::encrypt($data->mtid) }}">รายการรูปแบบผู้ใช้งาน</a>
</li>
<li>
	<a href="{{ url('routes/hotspot/adduserprofile') }}/{{ Crypt::encrypt($data->mtid) }}">เพิ่มรูปแบบผู้ใช้งาน</a>
</li>

<li class="uk-nav-header">ผู้ใช้งานอินเตอร์เน็ต</li>
<li>
	<a href="{{ url('routes/hotspot/usernet') }}/{{ Crypt::encrypt($data->mtid) }}">รายการผู้ใช้งานอินเตอร์เน็ต</a>
</li>
<li>
	<a href="{{ url('routes/hotspot/addusernet') }}/{{ Crypt::encrypt($data->mtid) }}">เพิ่มผู้ใช้งานอินเตอร์เน็ต</a>
</li>
<li>
	<a href="{{ url('routes/hotspot/addfileusernet') }}/{{ Crypt::encrypt($data->mtid) }}">อัพโหลดไฟล์ผู้ใช้งานอินเตอร์เน็ต</a>
</li>
<li>
	<a href="{{ url('routes/hotspot/addcardusernet') }}/{{ Crypt::encrypt($data->mtid) }}">สร้างบัตรผู้ใช้งานอินเตอร์เน็ต</a>
</li>
<li>
	<a href="{{ url('routes/hotspot/moveusernetroom') }}/{{ Crypt::encrypt($data->mtid) }}">ย้ายห้องผู้ใช้งานอินเตอร์เน็ต</a>
</li>