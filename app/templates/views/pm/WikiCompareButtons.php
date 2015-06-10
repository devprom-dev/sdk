<span id="document-compare-paginator">
	<?=str_replace('%1', count($documents), text(1711))?> &nbsp; &nbsp;
</span>

<a id="document-compare-prev" class="btn btn-small" href="javascript:;">
	<i class="icon-arrow-up"></i> <?=translate('Пред.')?>
</a>

<a id="document-compare-next" class="btn btn-small" href="javascript:;">
	<?=translate('След.')?> <i class="icon-arrow-down"></i> 
</a>

<script type="text/javascript">
	var documentCompareIds = <?=JsonWrapper::encode($documents)?>;

	$(document).ready( function() 
	{
		$('#document-compare-prev')
			.click( function() {
				$('#document-compare-block').show();
				
				if ( $(this).attr('disabled') ) return;
				
				var index = parseInt($('#document-compare-index').text()) - 1;
				if ( index < 0 ) return;

				$(this).attr('disabled', index <= 1);
				$('#document-compare-next').attr('disabled', index >= documentCompareIds.length);
				
				$('#document-compare-index').text(index);
				gotoRandomPage(documentCompareIds[index - 1], 0, false);
			})
			.attr('disabled', true);

		$('#document-compare-next')
			.click( function() {
				$('#document-compare-block').show();
				
				if ( $(this).attr('disabled') ) return;
				
				var index = parseInt($('#document-compare-index').text()) + 1;
				if ( index > documentCompareIds.length ) return;

				$('#document-compare-prev').attr('disabled', index <= 1);
				$(this).attr('disabled', index >= documentCompareIds.length);

				$('#document-compare-index').text(index);
				gotoRandomPage(documentCompareIds[index - 1], 0, false);
			})
			.attr('disabled', documentCompareIds.length < 1);
	});
</script>