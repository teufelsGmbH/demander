```typo3_typoscript
config.tx_demander {
    properties {
        property_name {
            table = tx_tablename_domain_model_blah
            field = pid
            operator = <
            additionalRestriction {
                tablename-filedname {
                    operator = =
                    value = 4
                }
            }
        }

        property_name2 {
            table = tx_tablename2_domain_model_blah
            field = uid
            operator = in
            additionalRestriction {
                tablename-fieldname {
                    operator = -
                    value {
                        0 = 2
                        1 = 4
                    }
                }
            }
        }

        property-name3 {
            table = tx_tablename3_domain_model_blah
            field = uid
            operator = =
        }
    }

    demands {
        property-name.value = 4

        or {
            property-name2.value = {
                0 = 3
                1 = 6
            }

            property-name3.value = 6
        }
    }
}
```
