page = PAGE
page {
    includeCSS.styles = /static/css/styles.css
    includeJS.scripts = /static/js/scripts.js

    meta {
        viewport = width=device-width, initial-scale=1.0
        X-UA-Compatible = IE=edge
        X-UA-Compatible.httpEquivalent = 1
        robots = index,follow
		robots {
			override = noindex,follow
			override.if.isTrue.field = no_search
			orderedStdWrap {
				10 {
					override {
						data = TSFE:applicationData|robots
					}
				}
			}
		}
        description {
            field = description
            override.data = TSFE:applicationData|description
        }
        keywords {
            field = keywords
            override.data = TSFE:applicationData|keywords
        }
        abstract {
            field = abstract
            override.data = TSFE:applicationData|abstract
        }
    }

    10 = LOAD_REGISTER
    10 {
        imageWidth = 1020
        imageWidthMd = 844
        imageWidthSm = 712
        imageWidthXs = 432
        defaultFirstHeaderLayout = 1
        defaultHeaderLayout = 2
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