<template>
  <b-row>
    <b-col sm="3" >
      <PracticeFilter
        :filters="filters"
        :active-filters="activeFilters"
        v-model="activeFilters"
        @input="currentPage = 1"
        :start-open="startOpen"
      />
    </b-col>
    <b-col sm="9" >
      <BaseRevisionTable
        :is-loading="isLoading"
        :rows="rows"
        :total="total"
        :pagination="true"
        :limit="perPage"
        :page="currentPage"
        @rows="handleRows"
        @page="handlePage"
      />
    </b-col>
  </b-row>
</template>

<script>
import _intersection from 'lodash.intersection'
// import {BRow, BCol} from 'bootstrap-vue/esm/components/layout'
import drupalMixin from './mixins/drupal-mixin.js'
import PracticeFilter from './BaseFilterMenu.vue'
import BaseRevisionTable from './BaseRevisionTable.vue'

export default {
  name: 'revision-filter',
  mixins: [ drupalMixin ],
  components: {
    'b-col': BCol,
    'b-row': BRow,
    PracticeFilter,
    BaseRevisionTable
  },
  props:{
      startOpen: {
          type: [Boolean],
          default: true
      },
      revisionPath: {
        type: String,
        default: '/api/practice/revisions/all?_format=json'
      }
  },
  computed: {
    total(){
      return this.activeRevisions.length
    },
    activeRevisions() {
      if (this.filterIds.length === 0) return this.revisions
      const filterKeys = Object.keys(this.activeFilters)
      const activeRevisions = []

      filterKeys.forEach(key => {
        const filterIds = this.activeFilters[key].map(f => f.id)
        activeRevisions.push(this.revisions.filter( r => _intersection(r.site_tags, filterIds).length > 0 ))
      })

      return _intersection(...activeRevisions.filter(a => {
        if (a.length > 0) return a
      }))

    },
    rows() {
      const startIndex = this.perPage * (this.currentPage - 1)
      const endIndex = startIndex + this.perPage
      const result = this.activeRevisions.slice(startIndex, endIndex)
      return result

    },
    filterIds(){
      const flat = []
      const filterKeys = Object.keys(this.activeFilters)
      filterKeys.forEach(k => this.activeFilters[k].forEach( f => flat.push(f.id)))
      return flat
    },
    computedFilters(){
      const filters = []
      const activeFilters = Object.entries(this.activeFilters)

      activeFilters.map(g => g[1].map(f => filters.push(f)))

      return filters

    },
    queryString(){
      let queryString = ''
      if(this.computedFilters.length > 0) {
        const groups = {}
        const filterQueries = this.computedFilters.map(f => {
          groups[f.type] = true
          return `&filter[${f.name}][condition][path]=site_tags.id&filter[${f.name}][condition][operator]=%3D&filter[${f.name}][condition][value]=${f.id}&filter[${f.name}][condition][memberOf]=${f.type}`
        })
        let groupFilters = ''
        for (let type in groups){
          groupFilters = groupFilters + `&filter[${type}][group][conjunction]=OR`
        }
        queryString = queryString + filterQueries.join("") + groupFilters
      }
      if(this.searchTerm.length > 0){
        queryString = queryString + `&filter[search][condition][path]=title&filter[search][condition][operator]=CONTAINS&filter[search][condition][value]=${this.searchTerm}`
      }
      if(queryString.length === 0) return ''
//      queryString = `&filter[filters][group][conjunction]=AND` + queryString
      return queryString

    }
  },
  data(){
    return {
      isLoading: false,
      currentPage: 1,
      perPage: 10,
      searchTerm: '',
      activeFilters: {},
      revisions: [],
      included: {},
      filters: {},
    }
  },
  methods: {
    async loadData(){
      this.revisions = await this.loadRevisions()
      this.users = await this.loadUsers()
    },
    handleRows(rows) {
      this.currentPage = 1
      this.perPage = rows
    },
    handlePage(page) {
      this.currentPage = page
    },
    getPriority(practice){
      const site_tags = practice.relationships.site_tags.data
      const priority = site_tags.find(d => d.type === 'taxonomy_term--priority')
      if(priority === undefined) return ''
      const result = this.included[priority.id]

      return result.attributes.name
    },
  },
  created: async function(){
    this.isLoading = true
    await this.loadFilters()
    await this.loadData()
    this.isLoading = false
  }
}
</script>

<style lang="scss">
/*
  @import "@/assets/style/_custom.scss";
  @import "~bootstrap/scss/variables";
  @import "~bootstrap/scss/root";
  @import "~bootstrap/scss/mixins";
  @import "~bootstrap/scss/utilities";
  @import "~bootstrap/scss/functions";
  @import "~bootstrap/scss/reboot";
  @import "~bootstrap/scss/type";
  @import "~bootstrap/scss/custom-forms";
  @import "~bootstrap/scss/buttons";
  @import "~bootstrap/scss/tables";
  @import "~bootstrap/scss/pagination";
  @import "~bootstrap/scss/dropdown";
  @import "~bootstrap/scss/buttons";
  @import "~bootstrap/scss/button-group";
  @import "~bootstrap/scss/input-group";
  //@import "~bootstrap/scss/images";
  @import "~bootstrap/scss/grid";
  @import "~bootstrap-vue/src/index.scss";
  */
</style>

