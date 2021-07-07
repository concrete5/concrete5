$(function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('.ccm-block-feature-item-hover-wrapper'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
})
