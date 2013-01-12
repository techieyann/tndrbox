$(document).ready(function(){

	$('#edit-business').hide();
	$('#edit-posting-form').hide();
	$('#add-posting-form').hide();

   $('a#edit-business-link').click(function(e){
	e.preventDefault();
     $('#business-info').animate({ height: 'hide'}, 'slow');
	 $('#edit-business').animate({ height: 'show'}, 'slow');
   });

   $('a#edit-business-cancel').click(function(e){
	   e.preventDefault();
     $('#business-info').animate({ height: 'show'}, 'slow');
	 $('#edit-business').animate({ height: 'hide'}, 'slow');
   });

	$('a#add-posting').click(function(e){
	e.preventDefault();
     $('#posting-border').animate({ height: 'hide'}, 'slow');
	 $('#add-posting-form').animate({ height: 'show'}, 'slow');
		$('#edit-delete-current-post').animate({ height: 'hide'}, 'slow');
   });

   $('#add-cancel-button').click(function(){
       $('#post-accordion').accordion('option', 'active', 1);
   });	


   $('a#edit-posting').click(function(e){
	e.preventDefault();
     $('#posting-border').animate({ height: 'hide'}, 'slow');
	 $('#edit-posting-form').animate({ height: 'show'}, 'slow');
	$('#add-post-header').animate({ height: 'hide'}, 'slow');
   });

   $('#edit-cancel-button').click(function(){
     $('#posting-border').animate({ height: 'show'}, 'slow');
	 $('#edit-posting-form').animate({ height: 'hide'}, 'slow');
	$('#add-post-header').animate({ height: 'show'}, 'slow');
   });

	$('a[id^=make-current]').click(function(e){
		e.preventDefault();
		$('a').find('[id^=make-current]').each(function(i){
			$(this).append('test' + i);
		});
	});

	$('.error').hide();  

	$('#edit-submit').click(function() {  
    // validate and process form here  
    $('.error').hide();  
      var title = $('input#edit-title').val();  
        if (title == '') {  
      $('#title_error').show();  
      $('input#title').focus();  
      return false;  
    }  
		var desc = $('input#edit-description').val();  
        if (desc == '') {  
      $('#desc_error').show();  
      $('input#description').focus();  
      return false;  
    }  
        var tag1 = $('input#edit-tag1').val();  
        if (tag1 == '') {  
      $('#tag_error').show();  
      $('input#tag1').focus();  
      return false;  
    }  
		var tag2 = $('input#edit-tag2').val();  
        if (tag2 == '') {  
      $('#tag_error').show();  
      $('input#tag2').focus();  
      return false;
    }  

	var tag3 = $('input#edit-tag3').val();  
        if (tag3 == '') {  
      $('#tag_error').show();  
      $('input#tag3').focus();  
      return false;  
    } 

	$('#edit-post-form').ajaxForm(function() { 
		$('#posting-border').load('home.php #posting-border');
		$('#posting-border').animate({ height: 'show'}, 'slow');
		$('#edit-posting-form').animate({ height: 'hide'}, 'slow');
		$('#add-post-header').animate({ height: 'show'}, 'slow');
    }); 
});

	$('.error').hide();  

	$('#add-submit').click(function() {  
    // validate and process form here  
    $('.error').hide();  
      var title = $('input#title').val();  
        if (title == '') {  
      $('#title_error').show();  
      $('input#title').focus();  
      return false;  
    }  
		var desc = $('input#description').val();  
        if (desc == '') {  
      $('#desc_error').show();  
      $('input#description').focus();  
      return false;  
    }  
        var tag1 = $('input#tag1').val();  
        if (tag1 == '') {  
      $('#tag_error').show();  
      $('input#tag1').focus();  
      return false;  
    }  
		var tag2 = $('input#tag2').val();  
        if (tag2 == '') {  
      $('#tag_error').show();  
      $('input#tag2').focus();  
      return false;
    }  

	var tag3 = $('input#tag3').val();  
        if (tag3 == '') {  
      $('#tag_error').show();  
      $('input#tag3').focus();  
      return false;  
    } 

	$('#add-post-form').ajaxForm(function() { 
		$('#postings-old-and-new').load('home.php #postings-old-and-new');
		$('#posting-border').animate({ height: 'show'}, 'slow');
		$('#add-posting-form').animate({ height: 'hide'}, 'slow');
		$('#edit-delete-current-post').animate({ height: 'show'}, 'slow');
    }); 
	});

$('.error').hide();  

	$('#edit-business-submit').click(function() {  
    // validate and process form here  
    $('.error').hide();
  
      var name = $('input#name').val();  
        if (name == '') {  
      $('#name_error').show();  
      $('input#name').focus();  
      return false;  
    }  

        var tag1 = $('input#tag_1').val();  
        if (tag1 == '') {  
      $('#tag1_error').show();  
      $('input#tag_1').focus();  
      return false;  
    }  

		var tag2 = $('input#tag_2').val();  
        if (tag2 == '') {  
      $('#tag2_error').show();  
      $('input#tag_2').focus();  
      return false;
    }  

	var address = $('input#address').val();  
        if (address == '') {  
      $('#address_error').show();  
      $('input#address').focus();  
      return false;  
    } 

	var zip = $('input#zip').val();  
        if (zip == '') {  
      $('#zip_error').show();  
      $('input#zip').focus();  
      return false;  
    } 

	$('#edit-business-form').ajaxForm(function() { 
		$('#business-info').load('home.php #business-info');
		$('#business-info').animate({ height: 'show'}, 'slow');
		$('#edit-business').animate({ height: 'hide'}, 'slow');
    }); 
	});

$('#tag1').autocomplete({source:'includes/tag_search.php'});
$('#tag2').autocomplete({source:'includes/tag_search.php'});
$('#tag3').autocomplete({source:'includes/tag_search.php'});
$('#tag_1').autocomplete({source:'includes/tag_search.php'});
$('#tag_2').autocomplete({source:'includes/tag_search.php'});
$('#edit-tag1').autocomplete({source:'includes/tag_search.php'});
$('#edit-tag2').autocomplete({source:'includes/tag_search.php'});
$('#edit-tag3').autocomplete({source:'includes/tag_search.php'});

$('#date').datepicker();
$('#edit-date').datepicker();
$( "#date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
$( "#edit-date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );

$('#post-accordion').accordion({heightStyle:'content', active:false, collapsible:true});


 });