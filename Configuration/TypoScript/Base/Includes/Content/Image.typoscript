tt_content.image {
    dataProcessing {
        10 {
            references {
                fieldName = assets
            }
        }
        20 >
        20 = TYPO3\CMS\Frontend\DataProcessing\GalleryProcessor
        20 {
            filesProcessedDataKey = files
            numberOfColumns.field = imagecols
        }
    }

    variables {
        imageWidth = TEXT
        imageWidth.value = ({register:imageWidth} / {field:imagecols} - {register:columnGap} / 2 * ({field:imagecols} - 1))
        imageWidth.stdWrap.insertData = 1
        imageWidth.prioriCalc = 1
        imageWidth.intval = 1

        imageWidthMd = TEXT
        imageWidthMd.data = register:imageWidthMd

        imageWidthSm = TEXT
        imageWidthSm.data = register:imageWidthSm

        imageWidthXs = TEXT
        imageWidthXs.data = register:imageWidthXs
    }
}