<?php

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('navigation');
if (is_array($image)) {
    $image = $image[0];
}?>

<div class="ccm-gathering-overlay image-sharing-link" id="image-sharing-link-<?php echo $this->gaiID; ?>">
	<div class="image-sharing-link-controls">
		<img class="overlay-header-image" src="<?=$image->getSrc()?>" style="max-width: 600px" />
		<div class="ccm-gathering-overlay-title ccm-gathering-thumbnail-caption">
			<div class="ccm-conversation-messages-header">
				<div style="width: 99%; margin: 0 auto; height: 3px; margin: 15px 0px 30px 0px; background: #abd9ff;"></div>
				<h4 class="ccm-conversation-message-count">3 Messages</h4>
			</div>
			<div class="ccm-conversation-add-new-message" rel="main-reply-form">
			<form method="post" class="main-reply-form clickable" data-dropzone-applied="true">
			<div class="ccm-conversation-avatar"><img class="u-avatar" src="/concrete/images/avatar_none.png" width="80" height="80" alt=""></div>
			<div class="ccm-conversation-message-form">
				<div class="ccm-conversation-errors alert alert-danger"></div>
				<div class="redactor_box"><ul class="redactor_toolbar"><li><a href="javascript:void(null);" title="Bold" class="redactor_btn_bold" tabindex="-1"></a></li><li><a href="javascript:void(null);" title="Italic" class="redactor_btn_italic" tabindex="-1"></a></li><li><a href="javascript:void(null);" title="Deleted" class="redactor_btn_deleted" tabindex="-1"></a></li><li class="redactor_separator"></li><li><a href="javascript:void(null);" title="Font Color" class="redactor_btn_fontcolor" tabindex="-1"></a></li><li class="redactor_separator"></li><li><a href="javascript:void(null);" title="Link" class="redactor_btn_link" tabindex="-1"></a></li></ul><div class="redactor_conversation-editor redactor_redactor_conversation_editor_2 redactor_form-control redactor_editor" contenteditable="true" dir="ltr" style="height: 80px;"><p><br></p></div><textarea id="cnvMessageBody" name="cnvMessageBody" class="conversation-editor redactor_conversation_editor_2 form-control" style="display: none;"></textarea></div>
				<input type="hidden" name="blockAreaHandle" id="blockAreaHandle" value="Column One">				<input type="hidden" name="cID" id="cID" value="1">				<input type="hidden" name="bID" id="bID" value="19">
			</div>
			</form>
			<img style="margin-top: 20px; margin-left: 47px; margin-bottom: 20px;" src="http://i.imgur.com/Fycxk9E.png" />
			<div style="width: 99%; margin: 0 auto; height: 2px; margin: 15px 0px 20px 0px; background: #efefef;"></div>
			<select class="ccm-sort-conversations" data-sort="conversation-message-list">
				<option value="date_desc">Recent</option>
				<option value="date_asc" selected="selected">Oldest</option>
				<option value="rating">Popular</option>
			</select>
			<div class="ccm-conversation-attachment-container" style="display: none;">
				<form action="<?= h(app(ResolverManagerInterface::class)->resolve(['/ccm/frontend/conversations/add_file'])) ?>" class="dropzone clickable" id="file-upload" data-dropzone-applied="true">
					<div class="ccm-conversation-errors alert alert-danger"></div>
					<input type="hidden" name="ccm_token" value="1390605625:07adc131bb627d0810ca52b3d87a8453">					<input type="hidden" name="blockAreaHandle" id="blockAreaHandle" value="Column One">					<input type="hidden" name="cID" id="cID" value="1">					<input type="hidden" name="bID" id="bID" value="19">				<div class="default message"><span>Drop files here or click to upload.</span></div></form>
			</div>
		</div>
			<div class="ccm-conversation-messages">

			<div data-conversation-message-id="1" data-conversation-message-level="0" class="ccm-conversation-message ccm-conversation-message-level0 ccm-ui">
					<a id="cnvMessage1"></a>
			<div class="ccm-conversation-message-user">
				<div class="ccm-conversation-avatar"><img class="u-avatar" src="/concrete/images/avatar_none.png" width="80" height="80" alt=""></div>
				<div class="ccm-conversation-message-byline">
					<span class="ccm-conversation-message-username">admin</span>
					<span class="ccm-conversation-message-divider">|</span>
					<span class="ccm-conversation-message-date">Posted on January 24, 2014</span>
				</div>

			</div>
			<div class="ccm-conversation-message-body">
				<p>This is an incredible piece of art that I wish to discuss at length.</p>		</div>
			<div class="ccm-conversation-message-controls">
				<div class="message-attachments">
								</div>
							<ul class="standard-message-controls">
					<!-- <li class="ccm-conversation-message-admin-control"><a href="#" data-submit="flag-conversation-message" data-conversation-message-id="1">Flag As Spam</a></li>
					<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="delete-conversation-message" data-conversation-message-id="1">Delete</a></li> -->

										<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="1">Reply</a></li>
								</ul>
				<span class="control-divider"> | </span>

			<ul class="nav nav-pills cnv-social-share">
		<li class="dropdown">
		<a class="dropdown-toggle" id="drop4" role="button" data-bs-toggle="dropdown" href="#">Share</a>
			<ul id="menu1" class="dropdown-menu" role="menu" aria-labelledby="drop4">
			<li><a class="shareTweet" target="_blank" href="https://twitter.com/intent/tweet?url=http://fivesevenmagic:8888#cnvMessage1">Twitter</a></li>
			<li><a class="shareFacebook" target="_blank" href="http://www.facebook.com/sharer.php?u=http://fivesevenmagic:8888#cnvMessage1">Facebook</a></li>
			<li><a data-message-id="1" rel="http://fivesevenmagic:8888#cnvMessage1" data-dialog-title="Link" class="share-permalink" href="#">Link</a></li>
			</ul>
		</li>
	</ul>
			<div class="conversation-rate-message-container"><i class="icon-thumbs-down conversation-rate-message" data-conversation-rating-type="down_vote"></i></div><div class="conversation-rate-message-container"><i class="icon-thumbs-up conversation-rate-message" data-conversation-rating-type="up_vote"></i></div>			<span class="ccm-conversation-message-rating-score" data-message-rating="1">0</span>
					</div>
		</div>
		<div style="margin-left: 30px;" data-conversation-message-id="2" data-conversation-message-level="1" class="ccm-conversation-message ccm-conversation-message-level1 ccm-ui">
					<a id="cnvMessage2"></a>
			<div class="ccm-conversation-message-user">
				<div class="ccm-conversation-avatar"><img class="u-avatar" src="/concrete/images/avatar_none.png" width="80" height="80" alt=""></div>
				<div class="ccm-conversation-message-byline">
					<span class="ccm-conversation-message-username">admin</span>
					<span class="ccm-conversation-message-divider">|</span>
					<span class="ccm-conversation-message-date">Posted on January 24, 2014</span>
				</div>

			</div>
			<div class="ccm-conversation-message-body">
				Agreed!		</div>
			<div class="ccm-conversation-message-controls">
				<div class="message-attachments">
								</div>
							<ul class="standard-message-controls">
					<!-- <li class="ccm-conversation-message-admin-control"><a href="#" data-submit="flag-conversation-message" data-conversation-message-id="2">Flag As Spam</a></li>
					<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="delete-conversation-message" data-conversation-message-id="2">Delete</a></li> -->

										<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="2">Reply</a></li>
								</ul>
				<span class="control-divider"> | </span>

			<ul class="nav nav-pills cnv-social-share">
		<li class="dropdown">
		<a class="dropdown-toggle" id="drop4" role="button" data-bs-toggle="dropdown" href="#">Share</a>
			<ul id="menu1" class="dropdown-menu" role="menu" aria-labelledby="drop4">
			<li><a class="shareTweet" target="_blank" href="https://twitter.com/intent/tweet?url=http://fivesevenmagic:8888#cnvMessage2">Twitter</a></li>
			<li><a class="shareFacebook" target="_blank" href="http://www.facebook.com/sharer.php?u=http://fivesevenmagic:8888#cnvMessage2">Facebook</a></li>
			<li><a data-message-id="2" rel="http://fivesevenmagic:8888#cnvMessage2" data-dialog-title="Link" class="share-permalink" href="#">Link</a></li>
			</ul>
		</li>
	</ul>
			<div class="conversation-rate-message-container"><i class="icon-thumbs-down conversation-rate-message" data-conversation-rating-type="down_vote"></i></div><div class="conversation-rate-message-container"><i class="icon-thumbs-up conversation-rate-message" data-conversation-rating-type="up_vote"></i></div>			<span class="ccm-conversation-message-rating-score" data-message-rating="2">0</span>
					</div>
		</div>
		<div data-conversation-message-id="3" data-conversation-message-level="0" class="ccm-conversation-message ccm-conversation-message-level0 ccm-ui">
					<a id="cnvMessage3"></a>
			<div class="ccm-conversation-message-user">
				<div class="ccm-conversation-avatar"><img class="u-avatar" src="/concrete/images/avatar_none.png" width="80" height="80" alt=""></div>
				<div class="ccm-conversation-message-byline">
					<span class="ccm-conversation-message-username">admin</span>
					<span class="ccm-conversation-message-divider">|</span>
					<span class="ccm-conversation-message-date">Posted on January 24, 2014</span>
				</div>

			</div>
			<div class="ccm-conversation-message-body">
				I like this pic a lot		</div>
			<div class="ccm-conversation-message-controls">
				<div class="message-attachments">
								</div>
							<ul class="standard-message-controls">
					<!-- <li class="ccm-conversation-message-admin-control"><a href="#" data-submit="flag-conversation-message" data-conversation-message-id="3">Flag As Spam</a></li>
					<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="delete-conversation-message" data-conversation-message-id="3">Delete</a></li> -->

										<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="3">Reply</a></li>
								</ul>
				<span class="control-divider"> | </span>

			<ul class="nav nav-pills cnv-social-share">
		<li class="dropdown">
		<a class="dropdown-toggle" id="drop4" role="button" data-bs-toggle="dropdown" href="#">Share</a>
			<ul id="menu1" class="dropdown-menu" role="menu" aria-labelledby="drop4">
			<li><a class="shareTweet" target="_blank" href="https://twitter.com/intent/tweet?url=http://fivesevenmagic:8888#cnvMessage3">Twitter</a></li>
			<li><a class="shareFacebook" target="_blank" href="http://www.facebook.com/sharer.php?u=http://fivesevenmagic:8888#cnvMessage3">Facebook</a></li>
			<li><a data-message-id="3" rel="http://fivesevenmagic:8888#cnvMessage3" data-dialog-title="Link" class="share-permalink" href="#">Link</a></li>
			</ul>
		</li>
	</ul>
			<div class="conversation-rate-message-container"><i class="icon-thumbs-down conversation-rate-message" data-conversation-rating-type="down_vote"></i></div><div class="conversation-rate-message-container"><i class="icon-thumbs-up conversation-rate-message" data-conversation-rating-type="up_vote"></i></div>			<span class="ccm-conversation-message-rating-score" data-message-rating="3">0</span>
					</div>
		</div>

		</div>
		</div>
	</div>
</div>
