

<li class="{{ $activeurl == 'auth/listuser' ? 'uk-active' : '' }} {{ $activeurl == 'auth/register' ? 'uk-active' : '' }} {{ $activeurl == 'auth' ? 'uk-active' : '' }}">
	<a href="{{ url('auth/listuser') }}">ผู้ใช้งานระบบ</a>
</li>

<li class="uk-parent {{ $activeurl == 'report' ? 'uk-active' : '' }}" data-uk-dropdown="" aria-haspopup="true" aria-expanded="false">
	<a href=""><i class="uk-icon-plus-square-o"></i> รายงาน</a>
	<div class="uk-dropdown uk-dropdown-navbar uk-dropdown-bottom">
		<ul class="uk-nav uk-nav-navbar">
			<li>
				<a href="{{ url('') }}"><i class="uk-icon-angle-right"></i> รายงาน 1</a>
			</li>
			<li>
				<a href="{{ url('') }}"><i class="uk-icon-angle-right"></i> รายงาน 2</a>
			</li>
			<li>
				<a href="{{ url('') }}"><i class="uk-icon-angle-right"></i> รายงาน 3</a>
			</li>
		</ul>
	</div>
</li>