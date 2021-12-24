# Regions

A lot of these regions have no styles and are just stubs to make it easier
to prototype full pages.

For example, at the time of this writing, region-site-footer has no 
styles or content. However, the pattern has a json file which includes
our standard site footer block.

This way, when you want to mock a page with a site footer, you can
just include this region/variant and it's all just there.

## Caveat

If a region template just calls out directly to 
components/_patterns/templates/region/region.twig it has probably not 
been wired into the drupal /templates folder since all the regions
in drupal basically just do that by default.