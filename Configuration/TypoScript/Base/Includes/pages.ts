page = PAGE
page {
    includeCSS.styles = /static/css/styles.css
    includeJS.scripts = /static/js/scripts.js

    10 = LOAD_REGISTER
    10 {

    }

    20 = FLUIDTEMPLATE
    20 {
        file =

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