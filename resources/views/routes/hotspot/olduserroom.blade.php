<div id="old-alluserroom">
<div class="uk-overflow-container">
	<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
		<thead>
			<tr>
        <th><input name="old_user_select_all" value="1" id="old-user-select-all" type="checkbox"></th>
				<th>ชื่อผู้ใช้งาน</th>
			</tr>
		</thead>
		<tbody>
            <input type="hidden" name="mtid" value="{{ $data->mtid }}">
			       @foreach($user as $value)
                <tr>
                    <td><input type="checkbox" name="oldid[]" value="{{ $value['.id'] }}"></td>
                    <td>{!! ((isset($value['name']))?$value['name']:'-') !!}</td>                            
                </tr>
            @endforeach
		</tbody>
	</table>
</div>
</div>

<script type="text/javascript">
    $(function(){

      
          /*  page1  */
          $('#old-user-select-all').on('click', function(){
              
              $('input:checkbox').not(this).prop('checked', this.checked);

          });
          $('#old-alluserroom tbody').on('change', 'input[type="checkbox"]', function(){
            // If checkbox is not checked      
            if(!this.checked){
               var el = $('#old-user-select-all').get(0);

               if(el && el.checked && ('indeterminate' in el)){
                  el.indeterminate = true;
               }
            }
          });


    });
</script>