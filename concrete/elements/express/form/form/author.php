<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Core::make("helper/form/user_selector");
$authorID = null;
if ($entry) {
    $author = $entry->getAuthor();

    if ($author) {
        $authorID = $author->getUserID();
    }
} else {
    $u = Core::make(Concrete\Core\User\User::class);
    $authorID = $u->getUserID();
}
?>

<div class="form-group">
    <?php if ($view->supportsLabel()) { ?>
        <label class="control-label form-label"><?=$label?></label>
    <?php } ?>
    <?=$form->selectUser('author', $authorID);?>
</div>
