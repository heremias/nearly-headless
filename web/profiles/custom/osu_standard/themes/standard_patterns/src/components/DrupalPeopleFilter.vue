<template>
  <div id="app">
    <b-container>
      <b-row>
        <b-col sm="3" v-if="hasFilters">
          <FilterMenu
            :filters="filters"
            :active-filters="activeFilters"
            :base-filters="baseFilters"
            v-model="activeFilters"
            @input="loadData"
            :start-open="startOpen"
          />
        </b-col>
        <b-col :sm="hasFilters ? 9 : 12" >
          <div style="min-height:1em;">
            <span v-show="isLoading">Loading...</span>
          </div>
          <BaseInput
            placeholder="Search names, titles, organizations, or other categories"
            :required="true"
            v-model="searchTerm"
          />

          <PeopleTable
            :people="computedPeople"
            :site-tags="indexedGroups"
            :is-loading="isLoading"
            ref="listing"
            class="listing"
          />
          <BasePagination
            :length="totalPages"
            v-model="currentPage"
          />
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import _debounce from 'lodash/debounce'
import {BContainer, BRow, BCol} from 'bootstrap-vue/esm/components/layout'
import PeopleTable from './DrupalPeopleTable.vue'
import BaseInput from '@umarketing/osu-design-system/src/molecules/BaseInput.vue'
import BasePagination from '@umarketing/osu-design-system/src/organisms/BasePagination.vue'
import FilterMenu from './BaseFilterMenu.vue'
import PracticeTable from './PracticeTable.vue'
import personApi from '../assets/util/personClient.js'

//import './assets/style/style.scss'

export default {
  name: 'people-filter',
  components: {
    'b-container': BContainer,
    'b-col': BCol,
    'b-row': BRow,
    BasePagination,
    PeopleTable,
    BaseInput,
    FilterMenu,
    PracticeTable
  },
  props:{
      startOpen: {
          type: [Boolean],
          default: true
      },
      domain: {
        type: String,
        default: () => ''
      },
      groups: {
        type: Array,
        default: () => []
      },
      baseFilters: {
        default: () => ([])
      },
      filters: {
        default: () => ({})
      },
      limit: {
        default: 5
      }
  },
  data(){
    return {
      isLoading: false,
      searchTerm: '',
      currentPage: 1,
      activeFilters: {},
      people: [],
      marker: {},
      included: {}
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.people.length / this.limit)
    },
    computedPeople() {
      const start = (this.currentPage -1) * this.limit
      return this.people.slice(start, start + this.limit)
    },
    indexedGroups() {
      return this.groups.reduce((groups, g) => {
        groups[g.type] = g
        return groups
      }, {})
    },
    hasFilters() {
      return Object.keys(this.filters).length > 0
    },
    computedFilters(){
      const filters = [...this.baseFilters]
      const activeFilters = Object.entries(this.activeFilters)

      activeFilters.map(g => g[1].map(f => filters.push(f)))

      return filters

    },
    checkBoxFilter() {
      if(!this.computedFilters.length) {
        return null
      }
        const filter = {}
        // loop over all selected filters
        this.computedFilters.forEach(f => {
          const name = f.name
          const type = f.type
          // create filter to match this site tag id
          const newFilter = {
            condition: {
              path: 'site_tags.id',
              operator: '=',
              memberOf: type,
              value: f.id
            }
          }
          // use vocab label to label the condition
          filter[`filter-${name}`] = newFilter
          // site tags terms are queried with OR clause when in the same vocabulary group
          // tags from other groups will be queried with AND clause (must match in both tag groups)
          filter[type] = {
            group: {
              conjunction: 'OR'
            }
          }
          // person must be published
        })
        return filter

    },
    searchFilter() {
      if(!this.searchTerm.length){
        return null
      }
      const filter = {}
      // return result if any field matches ('OR' condition)
      filter.search = {
        group: {
          conjunction: 'OR'
        }
      }
      // look for matches in these direct fields
      const searchFields = ['preferred_title', 'title', 'preferred_organization']
      searchFields.forEach(field => filter[`search-${field}`] = {
          condition: {
            path: field,
            memberOf: 'search',
            operator: 'CONTAINS',
            value: this.searchTerm
          }
      })
      // look for assigned vocab name matches in all taxonomy groups
        this.groups.forEach(g => filter[`${g.type}-search`] = {
          condition: {
            path: 'site_tags.name',
            memberOf: 'search',
            operator: 'CONTAINS',
            value: this.searchTerm
          }
        })
      return filter
    },
    queryObject() {
      //include needed relationships
      const include = 'site_tags,site_tags.parent.vid,site_tags.vid,image,image.image,image.image.uid'
      const status = { value: 1 }
      // filter using the checkboxes and search text
      const filter = { ...this.searchFilter, ...this.checkBoxFilter, status }
      return { include, filter}
    }
  },
  watch: {
    activeFilters(){
      this.isLoading = true
      this.loadData()
      this.mark()
    },
    searchTerm(){
      this.isLoading = true
      this.loadData()
      this.mark()
    },
    computedPeople(){
      this.mark()
    }
  },
  methods: {
    async mark() {
      await this.marker.unmark()
      if(this.searchTerm) {
        await this.$nextTick()
        console.log('marking ', this.searchTerm)
        await this.marker.mark(this.searchTerm)
      }
    },
    getPriority(practice){
      const site_tags = practice.relationships.site_tags.data
      const priority = site_tags.find(d => d.type === 'taxonomy_term--priority')
      if(priority === undefined) return ''
      const result = this.included[priority.id]

      return result.attributes.name
    },
    loadData: _debounce(async function(){
      this.isLoading = true
      const result = await personApi.get(this.queryObject)
      this.people = result
      this.isLoading = false
      this.currentPage = 1

    }, 750),
  },
  mounted: async function(){
    const Mark = require('mark.js')
    this.marker = new Mark(this.$refs.listing.$el)
  },
  created: function(){
    // this.loadFilters()
    this.isLoading = true
    this.loadData()
  }
}
</script>

<style lang="scss">
.listing mark {
 padding: 0;
 background-color: #FFB600;
 color: #202224;
}
</style>
