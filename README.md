```typo3_typoscript
  properties {
      property_name {
         table = tx_tablename_domain_model_blah
         field = pid
         operator = <
      }

      property_name2 {
          table = tx_tablename2_domain_model_blah
          field = uid
          operator = in
      }

      property_name3 {
          table = tx_tablename3_domain_model_blah
          field = uid
          operator = =
      }
  }

  demands {
    orderBy = name
    orderDirection = ASC
    property_name.value = 4

    or {
      property_name2.value {
         min = 3
         max = 6
      }

      property_name3.value = 6
      }
  }
```
