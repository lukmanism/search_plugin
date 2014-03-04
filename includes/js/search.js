var loaddata, loadresult;
$.ajax({
		async: false,
		url: "keywords.php?action=load", // fetching webservice
		beforeSend: function(xhr) {}
	}).done(function(data) {
		loaddata = $.parseJSON(data);
});
$.each(loaddata, function(k,v){ // load all keywords into html
	$('.keywords').append($('<li/>').text(v['keyword']).val(v['id']).hide());
});

// Search field assign on keyup & on keypress
$("#search").keyup(function(){
	$('#searchbtn').addClass('close');
    var filter = $(this).val();
    if(!filter){
        resetView();
        return;
    }
    var regex = new RegExp(filter, "i");
    var j = 0;
    $(".keywords li").each(function(){
        if ($(this).text().search(regex) < 0) { 
            $(this).hide();
        } else {
        	if(j <= 5){
                $(this).css("display", "inline-block");
	            j++;
        	}
        }
    });
}), $("#search").keypress(function(event) {
	if (event.which == 13) {
		$('#searchbtn').addClass('close');
		getSearch($(this), true);
		event.preventDefault();
	}
});

$('#searchbtn').click(function(){
    resetView(true);
});

$(".keywords li").click(function(){
	// console.log($(this));
	getSearch($(this), false);
	return false;
});

function getSearch(object, state){
	if(object.val() === ""){return false;} // avoid blank entree
    resetView();
	lazyIfElse = (typeof object.val() == 'string')? false: $('#search').val(object.text());
	column = (state)? '&kw=':'&id=';
	$.ajax({
			async: false,
			url: "keywords.php?action=search"+column+object.val(), // fetching webservice
			beforeSend: function(xhr) {}
		}).done(function(data) {
			if(data != 'null'){
				loadresult = $.parseJSON(data);
				$.each(loadresult, function(k,v){
					$.each(v, function(k1,v1){
						content = '';
						$.each(v1, function(k2,v2){
							content += '<li><span class="title">'+k2+'</span><span class="detail">'+v2+'</span></li>';
						});
						if(k == 0){ $('#results').append($('<h3/>').html('Search Result')); }
						if(k == 1){ $('#results').append($('<h3/>').html('Related search')); }
						$('#results').append($('<ul/>').html(content));
					});
				});
			} else {
				$('#results').append($('<h3/>').html('No result.'));
			}
			$("#results").easymark('highlight', object.text());

	});
}

function resetView(triggered){
	$(".keywords li").hide();
	$('#results').html('');
	if(triggered){			
    	$('#search').val('');
    	$('#searchbtn').removeClass('close');
	}
}