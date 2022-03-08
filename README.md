# Demander

Configurable, demand-based filtering framework with permalink-support for TYPO3.

## Documentation

For all kind of documentation which covers install to how to develop the extension:

| Source           | URL                                                                |
|------------------|--------------------------------------------------------------------|
| **Repository:**  | https://github.com/pixelant/demander                               |
| **Read online:** | https://docs.typo3.org/p/pixelant/demander/master/en-us |
| **TER:**         | https://extensions.typo3.org/extension/demander         |


## Example configuration

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
