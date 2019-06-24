<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php Loader::element('files/add_to_sets', array(
    'displayFileSet' => function ($fileset) use ($files) {
        $fp = \FilePermissions::getGlobal();
        foreach ($files as $f) {
            if (!$fp->canAddFileType(strtolower($f->getExtension()))) {
                return false;
            }
        }

        return true;
    },
    'getCheckbox' => function ($fileset) use ($files) {
        $checkbox = new HtmlObject\Input('checkbox');
        $checkbox->setAttribute('data-set', $fileset->getFileSetID());

        $input = new HtmlObject\Input('hidden', 'fsID:' . $fileset->getFileSetID(), 0);
        $input->setAttribute('data-set-input', $fileset->getFileSetID());

        $found = 0;
        foreach ($files as $f) {
            if ($f->inFileSet($fileset)) {
                ++$found;
            }
        }

        if ($found == 0) {
            // nothing
        } elseif ($found == count($files)) {
            $checkbox->checked('checked');
            $input->value(2);
        } else {
            $checkbox->indeterminate(1);
            $checkbox->class('tristate');
            $input->value(1);
        }

        $span = new HtmlObject\Element('span');
        $span->appendChild($checkbox)->appendChild($input);

        return $span;
    },
));
?>

<script type="text/javascript">
	$(function() {
		$('#ccm-file-set-list input.tristate').tristate({
			change: function(state, value) {
				var $input = $('input[data-set-input=' + $(this).attr('data-set') + ']');
				if (state === null) {
					$input.val(1);
				} else if (state === true) {
					$input.val(2);
				} else if (state !== true) {
					$input.val(0);
				}
			}
		});
		$('#ccm-file-set-list input[type=checkbox]:not(".tristate")').on('change', function() {
			var $input = $('input[data-set-input=' + $(this).attr('data-set') + ']');
			if ($(this).is(':checked')) {
				$input.val(2);
			} else {
				$input.val(0);
			}
		});
	});
</script>
