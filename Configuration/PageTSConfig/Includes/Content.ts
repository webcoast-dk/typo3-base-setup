TCAdefaults.tt_content.imagecols = 1

TCEFORM.tt_content {
    # tab: general
    header_layout.keepItems = 0,1,2,3,100
    header_position.disabled = 1
    date.disabled = 1
    header_link.disabled = 1
    subheader.disabled = 1
    CType.removeItems = textpic,bullets,table,menu_abstract,menu_categorized_content,menu_categorized_pages,menu_recently_updated,menu_related_pages,menu_section_pages,menu_sitemap_pages,html

    # tab: media
    imagewidth.disabled = 1
    imageheight.disabled = 1
    imageborder.disabled = 1
    imagecols.disabled = 1
    image_zoom.disabled = 1

    # tab: appearance
    layout.disabled = 1
    frame_class.disabled = 1
    space_before_class.disabled = 1
    space_after_class.disabled = 1
    sectionIndex.disabled = 1
    linkToTop.disabled = 1

    # tab: categories
    categories.disabled = 1
    # tab: notes
    rowDescription.disabled = 1

    # textmedia
    header_position.types.textmedia {
        disabled = 0
        removeItems = center,right,left
        addItems {
            above = LLL:EXT:typo3_base_setup/Resources/Private/Language/locallang_backend.xlf:tt_content.header_position.types.textmedia.above
        }
    }
    imagewidth.types.textmedia.disabled = 0

    # image
    imageorient.types.image.disabled = 1
    imagecols.types.image {
        disabled = 0
        keepItems = 1,2,3,4
    }
    imagewidth.types.image {
        disabled = 0
        label = LLL:EXT:typo3_base_setup/Resources/Private/Language/locallang_backend.xlf:tt_content.imagewidth.types.image
    }
    imageheight.types.image {
        disabled = 0
        label = LLL:EXT:typo3_base_setup/Resources/Private/Language/locallang_backend.xlf:tt_content.imageheight.types.image
    }

    # rename image content element
    CType.altLabels.image = LLL:EXT:typo3_base_setup/Resources/Private/Language/locallang_backend.xlf:tt_content.CType.image
}
