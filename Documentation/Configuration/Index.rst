.. include:: ../Includes.txt

.. _configuration:

========================
TypoScript Configuration
========================

Every part of the extension can be configured using TypoScript.

The Demander extension's TypoScript configuration is made at
:typoscript:`config.tx_demander`. The configuration has four main properties:

* :typoscript:`demandProviders`: An array of :php:`DemandProvider` class names.
* :typoscript:`properties`: An array of demand property definitions, roughly
  equivalent to database fields to filter by.
* :typoscript:`demands`: Pre-defined demands processed using
  :php:`Pixelant\Demander\DemandProvider\TypoScriptDemandProvider`.
* :typoscript:`ui`: Configuration for frontend UI components.
* There are also a limited number of other properties.

.. _configuration-typoscript-demandproviders:

:typoscript:`demandProviders`
-----------------------------

An array of :php:`DemandProvider` class names. Each provider fetches demand
information from a source and must implement
:php:`Pixelant\Demander\DemandProvider\DemandProviderInterface`.

* :php:`TypoScriptDemandProvider`: Fetches demands from
  :typoscript:`config.tx_demander.demands`.
* :php:`RequestDemandProvider`: Fetches demands from the body (POST data) of
  an HTTP request. The property values are fetched from the key `d`, e.g.
  :php:`$request->getQueryParams()['d']['myProperty']`.

Each :php:`DemandProvider` is ordered by the numeric key. Higher keys override
values from lower keys. All :php:`DemandProvider`s are always processed.

.. code-block:: typoscript

   demandProviders {
     40 = Pixelant\Demander\DemandProvider\RequestDemandProvider
     50 = Pixelant\Demander\DemandProvider\TypoScriptDemandProvider
   }

.. _configuration-typoscript-properties:

:typoscript:`properties`
------------------------

This array defines demand properties. That is, the array keys used by the
Demander extension and how to make their values into filtering restrictions in
the database.

.. _configuration-typoscript-properties-example:

Example
~~~~~~~

In this example a demand for `d[myProperty]=6` will filter for records with
`points` less than `6` and type set to `5`.

.. code-block:: typoscript

   properties {
     myProperty {
       table = tx_myextension_record
       field = points
       operator = <

       additionalRestriction {
         tx_myextension_record-type {
           operator = =
           value = 5
         }
       }
     }
   }

The resulting SQL restriction looks like:

.. code-block:: sql

   tx_myextension_record.points < 6 AND tx_myextension_record.type = 5

.. _configuration-typoscript-properties-properties:

Properties
~~~~~~~~~~

Properties within :typoscript:`config.tx_demander.properties.<propertyName>`.

.. _configuration-typoscript-properties-table:

table
'''''

:aspect:`Property`
   table

:aspect:`Data type`
   :ref:`t3tsref:data-type-string`

:aspect:`Description`
   The table name to apply the restriction to.

.. _configuration-typoscript-properties-field:

field
'''''

:aspect:`Property`
   field

:aspect:`Data type`
   :ref:`t3tsref:data-type-string`

:aspect:`Description`
   The field name to apply the restriction to.

.. _configuration-typoscript-properties-operator:

operator
''''''''

:aspect:`Property`
   operator

:aspect:`Data type`
   `=` / `>` / `>=` / `<` / `<=` / `-` / `in`

:aspect:`Description`
   The operator to use for comparison.

   * `=`: Equals
   * `>`: Greater than
   * `>=`: Greater than or equal to
   * `<`: Less than
   * `<=`: Less than or equal to
   * `-`: Between or equal to two values. The demand value must always be an
     array with two keys (`min` and `max`) containing the minimum and maximum
     values to use in the comparison.
   * `in`: The value must be one of the values in an array. The demand value
     must always be an array of values.

.. _configuration-typoscript-properties-additionalrestriction:

additionalRestriction
'''''''''''''''''''''

:aspect:`Property`
   additionalRestriction

:aspect:`Data type`
   array

:aspect:`Description`
   Additoinal restrictions to apply, for example if the filter applies to a
   certain record type only. Contains an array of table-field definition arrays
   with key :typoscript:`<tablename>-<fieldname>`. Available properties are:

   * :typoscript:`operator`: The restriction operator, see
     :ref:`configuration-typoscript-properties-operator`.
   * :typoscript:`value`: The value to apply to the restriction.

   **Example:**

   .. code-block:: typoscript

      additionalRestriction {
        tx_myextension_record-type {
          operator = =
          value = 5
        }
      }

   Configures this additional restriction:

   .. code-block:: sql

      tx_myextension_record.type = 5

.. _configuration-typoscript-demands:

:typoscript:`demands`
---------------------

Properties within
:typoscript:`config.tx_demander.demands.<propertyName>|and|or`.

Contains pre-defined, static demands processed using
:php:`Pixelant\Demander\DemandProvider\TypoScriptDemandProvider`. Compared to
most other demand providers, these demands will always be applied because they
are statically defined in TypoScript.

The keys can either be a property name or a conjunction (`and` or `or`). The
conjunction wraps another demand array, joining the restrictions using the
conjunction, rather than the default.

.. code-block:: typoscript

   demands {
     myProperty = 6
     or {
       aRangeProperty = 1-5
       aListProperty = 1,7,4
     }
   }

Property names contain the Demand extension's standard demand data definition,
i.e. :typoscript:`<propertyName>=<value>`.

The accepted values depend on the property operator:

* `=`: One string or scalar value.
* `>`: Scalar value.
* `>=`: Scalar value.
* `<`: Scalar value.
* `<=`: Scalar value.
* `-`: A range of scalar values with lowest and highest separated with a dash
  (i.e. :typoscript:`<lowest>-<highest>`, e.g. `1-5`).
* `in`: A comma-separated list of values or a TypoScript array of values.

  **Comma-separated list:**

  .. code-block:: typoscript

     aListProperty = 1,7,4

  **TypoScript array of values:**

  .. code-block:: typoscript

     aListProperty {
       10 = gold
       20 = silver
       30 = bronze
     }

.. _configuration-typoscript-ui:

:typoscript:`ui`
----------------

Properties within :typoscript:`config.tx_demander.ui.<propertyName>` define the
behavior of UI filter components used to create demands based on user input.
Such components could be a text field, a drop-down menu, a list of checkboxes,
or a slider.

.. _configuration-typoscript-ui-example:

Example
~~~~~~~

This example defines a drop-down menu with three options.

.. code-block:: typoscript

   ui {
     myProperty {
       label = My Property
       type = select
       values {
         a = Option A
         b = Option B
         c = Option C
       }
     }
   }

.. _configuration-typoscript-ui-properties:

Properties
~~~~~~~~~~

.. _configuration-typoscript-ui-label:

label
'''''

:aspect:`Property`
   label

:aspect:`Data type`
   :ref:`t3tsref:data-type-string`

:aspect:`Description`
   The UI component label. Also supports localization paths like
   :file:`LLL:EXT:myextension/Resources/Private/Language/locallang.xml:labelKey`.

.. _configuration-typoscript-ui-type:

type
''''

:aspect:`Property`
   type

:aspect:`Data type`
   `text` / `rangeText` / `select` / `checkbox` / `checkboxes` / `slider`
   / `rangeSlider`

:aspect:`Description`
   The type of UI element to render.

   * `text`: A text field
   * `rangeText`: Two text fields for defining numeric minimum and maximum
     values
   * `select`: A drop-down menu
   * `checkbox`: A checkbox (can only have one value)
   * `checkboxes`: A list of checkboxes
   * `slider`: A slider
   * `rangeSlider`: A slider with two knobs for defining minimum and maximum
     values

.. _configuration-typoscript-ui-values:

values
''''''

:aspect:`Property`
   values

:aspect:`Data type`
   array

:aspect:`Description`
   An array of one or more values values used in `select`, `checkbox` (only one
   value allowed), and `checkboxes`. Defined as `<value>=<label>`.

   .. code-block:: typoscript

      values {
        a = Option A
        b = Option B
        c = Option C
      }

.. _configuration-typoscript-other:

Other Properties
----------------

.. _configuration-typoscript-other-defaultconjunction:

defaultConjunction
''''''''''''''''''

:aspect:`Property`
   defaultConjunction

:aspect:`Data type`
   `and` / `or`

:aspect:`Default value`
   `and`

:aspect:`Description`
   The conjunction to use for demands. The default is to use `and`, which means
   results will match all demands. If set to `or` the results will match any of
   the demands.
