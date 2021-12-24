/**
 * Entry point to register Vue components for usage in twig templates.
 * Component(s) will not load unless they are detected in a template.
 * See examples below to add additional components as needed.
 */
import Vue from 'vue'

Vue.config.devtools = true

Vue.component(
  'people-filter',
  (resolve) => {
    require(['./components/DrupalPeopleFilter.vue'], resolve)
  }
)

Vue.component(
  'listing-practice',
  (resolve) => {
    require(['./components/DrupalPracticeFilter.vue'], resolve)
  }
)

Vue.component(
  'calendar-listing',
  // A dynamic import returns a Promise.
  (resolve) => {
    require(['@umarketing/osu-design-system/src/organisms/BaseCalendar.vue'], resolve)
  }
)

Vue.component(
  'activity-listing',
  // A dynamic import returns a Promise.
  (resolve) => {
    require(['./components/DrupalRevisionFilter.vue'], resolve)
  }
)

Vue.component(
  'activity-stream',
  // A dynamic import returns a Promise.
  (resolve) => {
    require(['./components/DrupalActivityStream.vue'], resolve)
  }
)
