lib.pageTitle = TEXT
lib.pageTitle {
    # use the page's title field
    field = title
    # override this with TSFE:altPageTitle if it is set
    override.data = TSFE:altPageTitle
    # append custom text if set, e.g. from an extension for pagination
    append = TEXT
    append {
        data = TSFE:applicationData|pageTitleAppend
        required = 1
        noTrimWrap = | - ||
    }
}

config {
    absRefPrefix = /
    disablePrefixComment = 1
    moveJsFromHeaderToFooter = 1

    disablePrefixComment = 1

    cache_clearAtMidnight = 1
    sendCacheHeaders = 1
    sendCacheHeaders_onlyWhenLoginDeniedInBranch = 0

    noPageTitle = 1
    pageTitle.cObject =< lib.pageTitle
    pageTitle.cObject {
        orderedStdWrap {
            10 {
                append = TEXT
                append {
                    data = leveltitle:0
                    if.isTrue.data = level
                    noTrimWrap = | - ||
                }
            }
        }
    }
}
