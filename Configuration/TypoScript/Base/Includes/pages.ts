page = PAGE
page {
    includeCSS {

    }
    includeJS {

    }

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
        backendLayout {
            data = levelfield:-1,backend_layout_next_level,slide
            override.field = backend_layout

            replacement {
                10 {
                    search = /^[\w\d_]+__[\w\d_]+\-/
                    replace =
                    useRegExp = 1
                }
            }

            orderedStdWrap {
                10 {
                    ifEmpty = Default
                }
            }
        }

        imageWidth = 1020
        imageWidthMd = 844
        imageWidthSm = 712
        imageWidthXs = 432
        columnGap = 16
        defaultFirstHeaderLayout = 1
        defaultHeaderLayout = 2
    }

    20 = FLUIDTEMPLATE
    20 {
        file =
        partialRootPaths {
            10 = EXT:typo3_base_setup/Resources/Private/Partials
        }

        layoutRootPaths {
            10 = EXT:typo3_base_setup/Resources/Private/Layouts
        }

        variables {
            contentMain =< lib.content.main
        }

        dataProcessing {
            # main menu
            10 = WEBcoast\Typo3BaseSetup\DataProcessing\MenuProcessor
            10 {
                as = menuMain
                entryLevel = 0
                levels = 3
            }

            # homepage data
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