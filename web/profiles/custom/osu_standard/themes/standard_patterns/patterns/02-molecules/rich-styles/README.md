# Rich Styles

These are styles supported by our rich text editor.
Basically, in ckeditor, when a user selects a particular 
type of text, they are presented with different options
in a style dropdown. These options just add classes.

For example, the rule below would allow users to choose "Button" 
when highlighting a link. Doing so would apply the rich-button
class.

```
a.rich-button|Button
```

Unlike other patterns, the html will never be "wired" to Drupal. 

The canonical list of styles supported in the Drupal editor
can be found in the 
[exported rich editor configuration](https://code.osu.edu/ucom/formats/blob/master/osu_rich/config/install/editor.editor.rich.yml
).

