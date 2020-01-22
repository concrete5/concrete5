$(function() {
    var hoverImgPictureParentSource;
    $('.ccm-image-block-hover')
        .mouseover(function() {
            $(this).attr('src', $(this).data('hover-src'));
            hoverImgPictureParentSource = hoverImgPictureParentSource || $(this).parent('picture').find('source');
            if (hoverImgPictureParentSource) {
                hoverImgPictureParentSource.attr('srcset', hoverImgPictureParentSource.data('hover-src'));
            }
        })
        .mouseout(function() {
            $(this).attr("src", $(this).data('default-src'));
            hoverImgPictureParentSource = hoverImgPictureParentSource || $(this).parent('picture').find('source');
            if (hoverImgPictureParentSource) {
                hoverImgPictureParentSource.attr('srcset', hoverImgPictureParentSource.data('default-src'));
            }
        });
});