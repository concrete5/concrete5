<?php
defined('C5_EXECUTE') or die("Access Denied.");

/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\Validation\CSRF\Token $token */
/** @var \Concrete\Core\View\View $view */
$bannedListEnabled = $bannedListEnabled ?? false;
?>
<form action="<?=$view->action('save')?>" method='POST'>
	<?php
    $token->output('update_banned_words');
    ?>
    <div class="ccm-dashboard-header-buttons">
        <a class='add_word btn btn-primary' href='#'><?=t('Add Word')?></a>
    </div>

    <div class="checkbox">
        <label>
		    <input value=1 name='banned_list_enabled' <?=$bannedListEnabled ? 'checked ' : ''?>type='checkbox'> <?=t('Disallow posts that include banned words?')?>
        </label>
    </div>

	<script class='word_template' type="text/template" charset="utf-8">
		<tr class='editing'>
			<th class='id'></th>
			<td class='word'><span></span><input name='banned_word[]' class="form-control"></td>
			<td style='text-align:right'><a href='#' class='save_word btn btn-primary'><?=t('Save')?></a></td>
		</tr>
	</script>

	<div class='banned_words_table' style='overflow:hidden'>
		<table class='banned_word_list table'>
			<thead>
				<tr>
					<th style='width:20px'>ID</th>
					<th><?=t('Word')?></th>
					<th style='width:200px;text-align:right'></th>
				</tr>
			</thead>
			<tbody>
				<?php
                foreach ($bannedWords as $word) {
                    if (!is_object($word)) {
                        continue;
                    }
                    ?>
					<tr>
						<th class='id'><?=$word->getID()?></th>
						<td class='word'><span><?=h($word->getWord())?></span><input style='display:none' name='banned_word[]' value='<?=h($word->getWord())?>'></td>
						<td style='text-align:right'>
                            <div class="btn-group">
                                <a href='#' class='edit_word btn btn-default'><?=t('Edit')?></a>
                                <a href='#' class='delete_word btn btn-danger'><?=t('Delete')?></a>
                            </div>
						 </td>
					</tr>
					<?php

                }
                ?>
			</tbody>
		</table>
	</div>

	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Save'), 'bannedwords-form', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>
<script>
var ctx = $('table.banned_word_list'), template = $('script.word_template'),
	getTemplate = function(){return $(template.text());},
	save = $("<a href='#' class='save_word btn btn-primary'><?=t('Save')?></a>"),
	edit = $("<div class=\"btn-group\"><a href='#' class='edit_word btn btn-default'><?=t('Edit')?></a><a href='#' class='delete_word btn btn-danger'><?=t('Delete')?></a></div>"),
	totalheight = ctx.parent().height();

if (!$('input[name=banned_list_enabled]').get(0).checked) {
	ctx.hide();
	ctx.parent().height(0);
}
$('input[name=banned_list_enabled]').click(function(){
	if (this.checked) {
		ctx.fadeIn(200);
		ctx.parent().animate({height:totalheight},200,function(){
			$(this).height('auto');
		});
	} else {
		totalheight = ctx.parent().height();
		ctx.fadeOut(200);
		ctx.parent().animate({height:0},200);
	}
});
$(".add_word").on('click', function(e) {
    ctx.find('tr.editing').find('a.save_word').click();
	var newWord = getTemplate();
	newWord.find('th.id').text(' ');
	newWord.appendTo(ctx.find('tbody'));

	ctx.find('td.word').children('input').focus();

	e.preventDefault();
	e.stopPropagation();
	return false;
});
ctx.on('click','a.edit_word',function(e){
	var me = $(this);
	ctx.find('tr.editing').find('a.save_word').click();
	me.closest('tr').addClass('editing').find('td.word').children('span').hide().end()
		.children('input').addClass('form-control').show().focus().end().end().end()
		.closest('td').empty().append(save.clone());

	e.preventDefault();
	e.stopPropagation();
	return false;
}).on('click','a.save_word',function(e){
	var me = $(this);
	var tr = me.closest('tr');

    tr.removeClass('editing')
        .find('td.word').children('span').text(tr.find('td.word').children('input').val()).show().end()
        .children('input').removeClass('form-control').hide().end().end().end();

    tr.find('td:eq(1)').empty().append(edit.clone());

	e.preventDefault();
	e.stopPropagation();
	return false;
}).on('blur', '.word', function(e) {
    var me = $(this);
	var tr = me.closest('tr');

    tr.removeClass('editing')
        .find('td.word').children('span').text(tr.find('td.word').children('input').val()).show().end()
        .children('input').removeClass('form-control').hide().end().end().end();

    tr.find('td:eq(1)').empty().append(edit.clone());

	e.preventDefault();
	e.stopPropagation();
	return false;
}).on('click','a.delete_word',function(e){
	if (confirm("<?=t('Are you sure you want to delete this word?')?>"))
		$(this).closest('tr').remove();
});
</script>
