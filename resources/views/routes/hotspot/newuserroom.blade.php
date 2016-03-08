<div id="new-alluserroom">
<div class="uk-overflow-container">
	<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
		<thead>
			<tr>
				<th>ชื่อผู้ใช้งาน</th>
			</tr>
		</thead>
		<tbody>
            <input type="hidden" name="mtid" value="{{ $data->mtid }}">
			       @foreach($user as $value)
                <tr>
                    <td>{!! ((isset($value['name']))?$value['name']:'-') !!}</td>                            
                </tr>
            @endforeach
		</tbody>
	</table>
</div>
</div>
