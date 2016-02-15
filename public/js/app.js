$(function(){

	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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

