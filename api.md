====================================================================
=                                                                  =
=                     API DOCUMENTATION                            =
=                                                                  =
====================================================================

********************************************************************

--------------------------------------------------------------------
-                        END POINTS LIST                           - 
--------------------------------------------------------------------
* /utilistaeurs ____________________________________________________
--------------------------------------------------------------------
* /utilisateurs/{reference} ________________________________________
--------------------------------------------------------------------
* /utilisateur/{reference} _________________________________________
--------------------------------------------------------------------

********************************************************************

--------------------------------------------------------------------
-                   QUERY PARAMS (in lowercase)                    - 
--------------------------------------------------------------------
-                 - #string. coloumns of table or foreign tables
-     select      - with or not suffix_table for culmuns of main tb
-                 - the foreign columns with suffix_table
-                 - use ',' like separator to select many columns
--------------------------------------------------------------------
-     linkTo      -
--------------------------------------------------------------------
-    compareTo    -
--------------------------------------------------------------------
-    operatorTo   -
--------------------------------------------------------------------
-    conectorTo   -
--------------------------------------------------------------------
-    foreigns     - #boolean. if is true all foreign table are selecteds
-                 - 
--------------------------------------------------------------------
-                 - #string. columns to apply the order
-     orderBy     - only man table columns, not foreign tble columns
-                 - default order column = created_at - in all tables
--------------------------------------------------------------------
-                 - #asc | desc. default order mode = asc
-                 - case 1: orderBy="name,lastname" and
-                 -     orderMode="" then orderMode="_defautl_vale_,_defautl_vale_"
-                 - case 2: orderBy="name,lastname,email" and
-    orderMode    -     orderMode="desc" then orderMode="desc,desc,desc" (repeat unique value)
-                 - case 3: orderBy="name,lastname,email" and
-                 -     orderMode="desc,asc" then orderMode="desc,asc,_defautl_vale_"
-                 - case 4: orderBy="name" and
-                 -     orderMode="desc,asc" then orderMode="desc" (trunc in orderBy length)
--------------------------------------------------------------------
- offset          - #int. start index of results registers
--------------------------------------------------------------------
- limit           - #int. end index of results registers
--------------------------------------------------------------------
- groupBy         -
--------------------------------------------------------------------


