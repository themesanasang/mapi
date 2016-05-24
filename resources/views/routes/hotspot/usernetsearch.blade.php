<div id="usermanage">
<div class="uk-overflow-container">
	<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
		<thead>
			<tr>
        <th><input name="user_select_all" value="1" id="user-select-all" type="checkbox"></th>
        <th>ห้อง</th>
				<!--<th>ผู้ให้บริการ</th>-->
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
                    <!--<td>{!! ((isset($value['server']))?$value['server']:'-') !!}</td>-->
                    <td>{!! ((isset($value['name']))?$value['name']:'-') !!}</td>
                    <td>{!! ((isset($value['profile']))?$value['profile']:'-') !!}</td>
                    <td>{!! ((isset($value['email']))?$value['email']:'-') !!}</td>		                            
                    <td>   
                        <a class="uk-button uk-button-success uk-button-mini" href="{{ url('routes/hotspot/editusernet') }}/{{ Crypt::encrypt($value['name']) }}/{{ Crypt::encrypt($data->mtid) }}">
                            <i class="uk-icon-edit"></i> แก้ไข
                        </a>
                        <a class="uk-button uk-button-danger uk-button-mini" href="{{ url('routes/hotspot/deleteusernet') }}/{{ Crypt::encrypt($value['.id']) }}/{{ Crypt::encrypt($data->mtid) }}">
                            <i class="uk-icon-remove"></i> ลบ
                        </a>                               
                    </td>
                </tr>
            @endforeach
		</tbody>
	</table>
</div>
</div>

<script type="text/javascript">
    $(function(){

          /**
          * select all user delete
          */
          $('#userdelbt').hide();
          /*  page1  */
          $('#user-select-all').on('click', function(){
              
              $('input:checkbox').not(this).prop('checked', this.checked);

              var n = $("input:checkbox:checked").length;
              if( n == 0 ){
                $('#userdelbt').hide();
              }else{
                $('#userdelbt').show();
              }
            });
          $('#usermanage tbody').on('change', 'input[type="checkbox"]', function(){
            // If checkbox is not checked      
            if(!this.checked){
               var el = $('#user-select-all').get(0);

               if(el && el.checked && ('indeterminate' in el)){
                  el.indeterminate = true;
               }
            }
            var n = $("input:checkbox:checked").length;
            if( n == 0 ){
              $('#userdelbt').hide();
            }else{
              $('#userdelbt').show();
            }
          });


    });
</script>