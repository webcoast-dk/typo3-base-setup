lib.content.main = CONTENT
lib.content.main {
    table = tt_content
    select {
        where = colPos=0
        orderBy = sorting
    }
    languageField = sys_language_uid
}

lib.contentElement {
    templateRootPaths.10 = EXT:typo3_default_setup/Resources/Private/Templates/Content
    partialRootPaths.10 = EXT:typo3_default_setup/Resources/Private/Partials/Content
    layoutRootPaths.10 = EXT:typo3_default_setup/Resources/Private/Layouts/Content

    variables {
        cObjNumber = TEXT
        cObjNumber.data = cObj:parentRecordNumber

        disableHeaderLink = TEXT
        disableHeaderLink.value = 1
    }
}

<INCLUDE_TYPOSCRIPT: source="DIR:EXT:typo3_default_setup/Configuration/TypoScript/Base/Includes/Content" extensions="ts">
<INCLUDE_TYPOSCRIPT: source="DIR:EXT:typo3_default_setup/Configuration/TypoScript/Base/Includes/Extensions" extensions="ts">
