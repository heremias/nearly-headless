# Fields

In Drupal, a field template renders a field label (sometimes) 
and one or more field items which could by anything. As such,
all fields should generally have the same or similar html.

## The Problem

In Pattern Lab, if you want to create an accurate json file 
(as in mirroring drupal's render pipeline) you need to create
fields of items of media and taxonomy entities which have 
fields sometimes of other entities...

The result is 100+ line json files (a special hell).
This problem is solved by creating.

It also becomes confusing when fields have multiple renderings
(and they always do) as these alternate renderings
are represented only in the caller's json data. IE, a 
content_article~full.json file knows how its
teaser_image field should render in the "full" display mode, 
but the teaser image has no idea if there's no pattern for it. 
As fields are likely to share renderings across content types, 
this leads to massively duplicated code for pattern data.

**Bad Example**

Includes a content field with an arbitrary configuration.
This may be ok if you need to mock a page with specific
data for a client.

```
"content": {
  "include()": {
    "pattern": "atoms-field-content",
    "with": {
      "items": [
        {
          "content": {
            "include()": {
              "pattern": "molecules-formatted-text",
              "with": {
                "content": {
                   "body": "<p>Rooted in formal groups like Council on Academic Excellence for Women and Critical Difference for Women, and informal groups such as the \"grassroots network\" and Association of Faculty and Professional Women (now the Association for Staff and Faculty Women), women across campus carried out these efforts on behalf of all women at Ohio State. Regardless of the group, the goal was the same, to articulate what was needed within the University so that the climate was positive for women and so that women could succeed and advance. Basedin part on these efforts, President Gee appointed a task force for The Women's Place in 1996 and the initial concept for the function was developed.</p><p>The Ohio State University has actively recruited women and minorities, but not retained them at the same rate, thus the percentage of women in tenured faculty and administrative leadership positions has not increased markedly over the years. In 1998, President Brit Kirwan and Provost Ed Ray recognized the potential value of The Women's Place in addressing climate issues that impact retention.</p><p>The Women's Place opened its doors in January of 2000. In its first years of service it created a website that served as a clearinghouse for information for women on campus; provided networking opportunities; served as a resource for faculty to support retention; implemented thePresident'sCouncilonWomen; collected data on climate issues for women; and began publishing theAnnualReportontheStatusofWomenat OSU.</p><p><strong>Today, we continue to build upon our four areas of focus. </strong></p><h2>Policy</h2><p>The Women's Place makes Ohio State a cutting edge institution that supports women faculty and staff members'opportunities for achievement. The Women's Place works to create a culture characterized by equity, freedom and dignity for all people in which all can thrive and make their full contributions.</p><p>The Women's Place work includes policy development, for example: <ul><li>Extension of the tenure clock for birth, adoption or other issues.</li><li>Sexual harassment.</li><li>Consensual sexual relationships.</li><li>Search committee training.</li></ul></p><h2>Culture</h2><p>The Women's Place works to catalyze change in the university climate by introducing theArtofHostingMeaningfulConversationsto campus, sponsoring a series of training opportunities that have allowed several hundred faculty and staff members to learn this way of bringing equity of voice, respect and acknowledgement of group wisdom to planning and decision-making processes.</p>"
                }
              }
            }
          }
        }
      ]
    }
  }
}
```

The solution is to create a pattern per field and pseudo
pattern per non-default (rendered) display mode and bundle 
(if different). 

**Good Example**

Includes the "content" field's full display.

```
"content": "fields-content-full"
```
 
## Field Pseudo Pattern Conventions

A field can be rendered multiple ways in different bundles 
(content_article, event, etc) and different view modes 
(full, teaser, search, small, medium, large). 

When a field is rendered different ways, we use pseudo-patterns
to stub things out using this pattern. By convention, different
bundles will normally render a field the same in the same 
display modes.

```
{entity-type}-{field}~{view-mode}-{bundle}.json
```

### Examples:

```
node-email.json                      
- "default" view mode.

node-email~full.json                 
- "full" view mode normally.

node-email~full-content-article.json 
- "full" view mode in "content_article" (only if different)

```