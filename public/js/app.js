$(function(){

	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
	});


  



  /**
  * check show chart
  */
  var chkindex = window.location.pathname;

  /* user admin */
  if( chkindex == '/mapi/systems' ){

    $.getJSON( "getchart01", function( json ) { 
        var data = [];
        for (var i=0; i < json.length; i++) {
          data.push([json[i].name, parseInt(json[i].numroutes)]);
        }

        //console.log(data);

        $('#chart01').jqplot([data], {
            title:'จำนวนอุปกรณ์เชื่อมต่อ ต่อผู้ใช้งาน',
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
                rendererOptions: {
                    varyBarColor: true
                },
                pointLabels: { 
                    show: true,
                    formatString: '%s ตัว',
                    formatter: $.jqplot.LabelFormatter 
                }
            },
            axes:{
                xaxis:{
                    renderer: $.jqplot.CategoryAxisRenderer
                }
            }
        });
    });
  
 
    

  }

  /* user other */
  if( chkindex == '/mapi/home' ){

  }
  






  /**
  * form search user
  */
  $('#searchuser').on('change', function () {
    var room = $('#searchuser').val();
    var mtid = $('#mtid').val();

    $.get( room+"/"+mtid, function( data ) {
      $( "#tableuser" ).html( data );
    });
  });






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
  $('#ckdelall').on('click', function(e){
    var data = $('#usermanage tbody input').serialize();
      
    $.ajax({
          type: "POST",
          url: "userchkdelete",
          data: data,
          success: function(data) {
              window.location.reload(true); 
          }
    });
  });

  


	
  
	/*resizeDiv();
	window.onresize = function(event) {
	resizeDiv();
	}*/
  
  
});


function resizeDiv() {
  vpw = $(window).width();
  vph = $(window).innerHeight();
  $('.app-container').css({'height': vph + 'px'});
}



/**
* ลบผู้ใช้งานระบบ
*/
function deluser(id){
	$.ajax({
    type: "POST",
    url: "deleteuser",
    data: {'userid':id},
    success: function(data) {
    		window.location.reload(true);	
    }
  });
}

/**
* ลบ MT
*/
function delroutes(id)
{
  $.ajax({
    type: "POST",
    url: "routes/deleteroutes",
    data: {'mtid':id},
    success: function(data) {
        window.location.reload(true); 
    }
  });
}

