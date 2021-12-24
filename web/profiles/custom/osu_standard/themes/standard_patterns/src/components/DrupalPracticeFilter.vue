<template>
  <div id="app">
    <b-container>
      <b-row>
        <b-col sm="3" >
          <PracticeFilter
            :filters="filters"
            :active-filters="activeFilters"
            v-model="activeFilters"
            @input="loadData"
            :start-open="startOpen"
          />
        </b-col>
        <b-col sm="9" >
          <PracticeTable
            :practices="practices"
            v-model="searchTerm"
            :is-loading="isLoading"
          />
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import axios from 'axios'
import _debounce from 'lodash/debounce'
import {BContainer, BRow, BCol} from 'bootstrap-vue/esm/components/layout'
import drupalMixin from './mixins/drupal-mixin.js'
import PracticeFilter from './BaseFilterMenu.vue'
import PracticeTable from './PracticeTable.vue'

//import './assets/style/style.scss'

export default {
  name: 'practice-filter',
  mixins: [ drupalMixin ],
  components: {
    'b-container': BContainer,
    'b-col': BCol,
    'b-row': BRow,
    PracticeFilter,
    PracticeTable
  },
  props:{
      startOpen: {
          type: [Boolean],
          default: true
      }
  },
  computed: {
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
      return `${queryString}&filter[status][value]=1`

    }
  },
  data(){
    return {
      isLoading: false,
      searchTerm: '',
      activeFilters: {},
      practices: [],
      included: {},
      filters: {},
    }
  },
  watch: {
    activeFilters(){
      this.loadData()
    },
    searchTerm(){
      this.loadData()
    }
  },
  methods: {
    getPriority(practice){
      const site_tags = practice.relationships.site_tags.data
      const priority = site_tags.find(d => d.type === 'taxonomy_term--priority')
      if(priority === undefined) return ''
      const result = this.included[priority.id]

      return result.attributes.name
    },
    loadData: _debounce(async function(){
      const queryString = this.queryString
      const path = '/jsonapi/node/practice?include=site_tags'

      this.isLoading = true

      const result = await axios.get(this.domain + path + queryString)

      if(Array.isArray(result.data.included)){
        result.data.included.map(i => {
          this.$set(this.included, i.id, i)
        })
        this.practices = result.data.data
        this.practices.map(p => {
          p.priority = this.getPriority(p)
          p.title = p.attributes.title
          return p
        })
      }else{
        this.practices = []
      }
      this.isLoading = false

    }, 750),
  },
  created: function(){
    this.loadFilters()
    this.loadData()
  }
}
</script>


