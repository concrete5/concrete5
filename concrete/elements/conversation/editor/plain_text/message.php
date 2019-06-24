<?php defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper('form');
echo $form->textarea($editor->getConversationEditorInputName(), $editor->getConversationEditorMessageBody(), array('class' => 'unbound plaintext_conversation_editor_'.$editor->getConversationObject()->getConversationID()));
?>
<script type="text/javascript">
	var me = $('textarea.unbound.plaintext_conversation_editor_<?=$editor->getConversationObject()->getConversationID()?>').first().removeClass('unbound');
	(function($,window,me){
		var obj = window.obj;
		ConcreteEvent.bind('ConversationSubmitForm',function(){
			me.val('');
			$('.preview.processing').each(function(){
				$('input[rel="'+ $(this).attr('rel') +'"]').remove();
				$(this).remove();
			});
			if($('.ccm-conversation-attachment-container').is(':visible')) {
				$('.ccm-conversation-attachment-container').toggle();
			}
		});
		me.keydown(function(e) {
			ConcreteEvent.fire('conversationsTextareaKeydown', e, obj.$element.get(0));
			if (e.keyCode == 40) {
				ConcreteEvent.fire('conversationsTextareaKeydownDown',e,obj.$element.get(0));
				if (obj.dropdown.active) return false;
			} else if (e.keyCode == 38) {
				ConcreteEvent.fire('conversationsTextareaKeydownUp',e,obj.$element.get(0));
				if (obj.dropdown.active) return false;
			} else if (e.keyCode == 13) {
				ConcreteEvent.fire('conversationsTextareaKeydownEnter',e,obj.$element.get(0));
				if (obj.dropdown.active) return false;
			}
		}).keyup(function(e) {
			if (e.keyCode == 40) return;
			var caret = obj.tool.getCaretPosition(me.get(0)),
				string = me.val().substr(0,caret).split(' ').pop(),
				matches = [];
			// If this is a mention string
			if (obj.tool.testMentionString(string)) {
				matches = obj.tool.getMentionMatches(string.substr(1),obj.options.activeUsers);
			}
			matches.map(function(u,p){
				matches[p] = new obj.tool.MentionUser(u); // Convert strings to objects
				matches[p].caretPos = caret;
				matches[p].string = string;
				matches[p].textarea = me;
			});
			// Fire mention event regardless of whether there is data or not.
			ConcreteEvent.fire('conversationsMention',{
					obj: obj,
					items: matches,
					bindTo: me.get(0),
					coordinates: {
						x: me.offset().left,
						y: me.offset().top + me.height() + 10
					}
				},
				obj.$element.get(0)
			);
		});
		// Bind to item selection event
		ConcreteEvent.bind('ConversationMentionSelect', function(e, data){
				var selected = data.item;
				if (!selected.textarea.is(me)) return;
				var start = me.val();
				var fin = start.substr(0,selected.caretPos - selected.string.length + 1) + selected.getName() + start.substr(selected.caretPos);
				me.val(fin);
				obj.tool.setCaretPosition(me.get(0),selected.caretPos - selected.string.length + 1 + selected.getName().length);
			},
			me.get(0)
		);
	})(jQuery,window,me)
</script>
