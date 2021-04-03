<?php
/*put this at the bottom of the page so any templates
 populate the flash variable and then display at the proper timing*/
?>
<div class="container" id="flash">
    <?php $messages = get_messages(); ?>
    <?php if ($messages): ?>
        <?php foreach ($messages as $msg): ?>
            <div id="message" class="row bg-secondary justify-content-center">
                <p><?php echo $msg; ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<!--Used to move flash messages to the bottom of the form-->
<script type="text/javascript" src="static/js/flash_ele.js"></script>