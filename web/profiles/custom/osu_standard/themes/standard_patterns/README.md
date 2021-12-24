# standard_patterns
This project builds the theme that is used in the `standard` Drupal distribution.

## Structure
Existing pattern-lab components exist under the directory `/patterns`.
|Folder/File |Description|
|-------|-----------|
|`/patterns`|Existing pattern-lab components are here. All javascript files are combined together and served on the front end. `/patterns/style.scss` is the entry point for a seperate bundle which is compiled into CSS and also served to the front end site.|
|`/src/Processor`|This is a Drupal folder.|
|`/src/index.js`|The entry point for custom Vue components. New components must be registered here.|
|`/src/components`|The custom vue components are located here.|

## Adding new components
Here are the steps necessary to adding a new component.
1 Develop component (can import into src/App.vue to dev custom components )
2 Register in `/src/index.js`
3 Register the paragraph in Drupal by adding to `$vueParagraphs` array within `standard_patterns.libraries.yml`
  - (optional) add seperate paragraph specific function to load any needed data (see `standard_patterns_preprocess_paragraph__calendar_listing` for example)
4 Create template for component using `"<paragraph type> + <paragraph id>"` convention to assign the id of the component container. Create the template for the component assigning any passed data as props. (see `templates/paragraphs/listings/paragraph--calendar-listing.html.twig` for an example).

## Project setup
```
npm install
```

### Compiles for development
You must run the `robo local:start` in the project root to start the Drupal instance. The Drupal CMS will clear cache on each rebuild.
```
npm run watch
```

### Compiles and minifies for production
```
npm run build
```

### Lints and fixes files
```
npm run lint
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).
