DropIt
======

## Description
Drag and drop layout management framework for WordPress. Comes with a set of widgets aka "drops".


## Implementation thoughts not to forget:

For backbone.js model grab all the registered drops, parse their properties and serve as a collection.
Iterate through collection to set available options for a single droppable block ( @todo terminology? ).

Possible options:
+ name: A name of the instance ( may be not)
+ type: One of the registered types (e.g. static, post, or a loop (pretty much anything wp_query can do ) ) (extendable with filters or by naming convention)
+ arguments: for post, it's post id, for loop( any valid wp_query argument that could be easily mapped to UI. Filters for others )
+ content (if static). Probably WYSIWYG editor to put some basic stuff.

Frontend:
+ A set of default templates for default drops. Not sure if it's reasonable to include any CSS.
+ Ability to set your own templates per drop.

Backend Workflow:

Some use cases:
+ User wants to add Drop It Zone on some taxonomy archive (add conditionals where should the zone be displayed ( e.g. is_category( 'Pancakes' ) ). UI/filters for it.
+ User is in the post edit screen, and wants to add a zone( UI that drops shortcode, pretty much like Media UI )
