page = PAGE
page {
    includeCSS.styles = /static/css/styles.css
    includeJS.scripts = /static/js/scripts.js

    10 = LOAD_REGISTER
    10 {
        imageWidth = 1020
        imageWidthMd = 844
        imageWidthSm = 712
        imageWidthXs = 432
    }

    20 = FLUIDTEMPLATE
    20 {
        file =

        variables {
            contentMain = < lib.content.main
        }

        dataProcessing {
            10 = KappHamburg\Typo3DefaultSetup\DataProcessing\MenuProcessor
            10 {
                as = menuMain
                entryLevel = 0
                levels = 3
            }

            20 = TYPO3\CMS\Frontend\DataProcessing\DatabaseQueryProcessor
            20 {
                table = pages
                uidInList.data = leveluid:0
                pidInList = 0
                as = homepage
            }
        }
    }

    1000 = RESTORE_REGISTER
}