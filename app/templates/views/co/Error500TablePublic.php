<?php

$view->extend('core/Page.php');

?>
<div class="row-fluid">
    <div class="span3"></div>
    <div class="span6">
        <br/><br/><br/><br/>
        <section class="content">
            <div class="container-fluid">
                <div class="row-fluid">
					<form>
						<fieldset>
							<legend> 
							    500 / Internal Server Error
							</legend>
						</fieldset>
						<p><?=text(1315)?></p>
					    <br/>
                        <p><?=htmlentities($text, ENT_QUOTES | ENT_HTML401, APP_ENCODING)?></p>
					    <br/>
					</form>
                </div>
            </div>
        </section>
    </div>
    <div class="span3"></div>
</div>

<div class="clearfix"></div>
