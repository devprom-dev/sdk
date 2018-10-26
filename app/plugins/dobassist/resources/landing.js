//    $('.signup').validate({
//			submitHandler: function() {
//				createInstance();
//			}
//    });
function createInstance()
{
	//_gat._getTracker("UA-10541243-1")._trackEvent('devopsboard-start', 'blank', 'quick-form');
	$.ajax({
		type: "GET",
		url: 'http://hq.devopsboard.com/co/command.php?class=createinstance&namespace=dobassist&action=1',
		data: {
			instance: $('#company-name').val(),
			email: $('#your-email').val(),
			username: $('#your-name').val()
		},
		dataType: "html",
		success: 
			function(result) {
				try {
					data = jQuery.parseJSON(result);
					if ( data.state == 'error' ) {
						$('.signup').validate().showErrors({'company_name':data.message});
						return;
					}
					window.location = data.object;
				}
				catch( e ) {
					$('.signup').validate().showErrors({'company_name':result});
				}
			},
		error: 
			function(xhr, status, error) {
				$('.signup').validate().showErrors({'company_name':error});
			}
	});
}