<template>
<div>
  <h2 class="title">Activity Stream</h2>
  <p class="title"> Best practices that have been updated in the past six months </p>
  <BaseRevisionTable
    :rows="practices"
    :is-loading="isLoading"
  />
</div>
</template>

<script>
/*
import axios from 'axios'
import moment from 'moment'
*/
import drupalMixin from './mixins/drupal-mixin.js'
import BaseRevisionTable from './BaseRevisionTable.vue'

export default {
  name: 'activity-stream',
  mixins: [ drupalMixin ],
  components: { BaseRevisionTable },
  data(){
      return {
          isLoading: false,
          taxonomies: {},
          practices: [],
          users: {}
      }
  },
  methods: {
    async loadData(){
      this.isLoading = true
      this.users = await this.loadUsers()
      this.practices = await this.loadRevisions()
      this.isLoading = false
    },
    async loadTags(){
      const taxonomies = {
        "Priority Level": 'priority',
        "Media Type": 'media_type',
        "Impact": 'impact',
        "Role": 'roles'
      }
      Object.keys(taxonomies).map(t => this.taxonomies[t] = {})
    }
  },
  created: function(){
    this.loadData()
    this.loadTags()
  }
}
</script>

<style scoped>
  .title {
    text-align: center;
    font-size: 1.7rem;
    margin: 0 auto;
    text-align: center;
  }

  h2.title {
    color: #333;
    font-size: 2.5rem;
    margin-top: 0;
    font-family: proximanova,Arial,san-serif;
    font-weight: 700;
    margin-bottom: .5rem;
    line-height: 1.1em;
  }

  .fa-bell {
    color: #DCAA38;
  }
  .badge-warning {
    background-color: #FFEAAE;
  }
</style>
