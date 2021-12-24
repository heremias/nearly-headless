<template>
  <div>
    <b-form-group
      class="search-bar-container"
    >
      <span class="title">Search By Keyword</span>
      <b-form-input aria-label="Search Bar" @input="$emit('input', $event)" placeholder="meta title, captions, etc"></b-form-input>
    </b-form-group>
    <BaseTable
      :search="false"
      :pagination="false"
      :bordered="false"
      :rows="sortedRows"
      :headers="fields"
      :is-loading="isLoading"
      v-on="$listeners"
      v-bind="$props"
      @sort="handleSort"
    >
    <template v-slot:row="{row}">
      <td> <a :href="row.attributes.path ? row.attributes.path.alias : '#'">{{row.attributes.title}}</a> </td>
      <td class="text-right">{{row.priority}}</td>
    </template>
    </BaseTable>
    <b-row>
      <b-col>
        <p> Showing {{startRow}}-{{lastRow}} of {{rows}}</p>
      </b-col>
      <b-col>
        <b-pagination
          v-model="currentPage"
          :total-rows="rows"
          :per-page="perPage"
          aria-controls="listing-practice-table"
          prev-text="Previous"
          next-text="Next"
          v-show="rows > perPage"
          hide-goto-end-buttons
        ></b-pagination>
      </b-col>
    </b-row>
  </div>
</template>

<script>
  import "bootstrap-table/dist/bootstrap-table.css"
  import {BFormGroup} from 'bootstrap-vue/esm/components/form-group'
  import {BFormInput} from 'bootstrap-vue/esm/components/form-input'
  import {BPagination} from 'bootstrap-vue/esm/components/pagination'
  import {BCol} from 'bootstrap-vue/esm/components/layout'
  import {BRow} from 'bootstrap-vue/esm/components/layout'
  import {BAlert} from 'bootstrap-vue/esm/components/alert'
  import qs from 'querystringify'
  import Url from 'url-parse'
  import BaseTable from '@umarketing/osu-design-system/src/organisms/BaseTable.vue'


  export default {
    components:{
      "b-form-group": BFormGroup,
      "b-form-input": BFormInput,
      "b-pagination": BPagination,
      BaseTable,
      "b-row": BRow,
      "b-col": BCol,
      "b-alert": BAlert
    },
    props: {
      isLoading: {
        type: Boolean,
        default: false
      },
      practices: Array
    },
    data(){
      return {
        currentPage: 1,
        perPage: 25,
        currentSort: 'priority',
        fields: {
          title: {
            label:'Best Practice',
            field:'title',
            sortable: true
          },
          priority: {
            label:'Priority',
            field:'priority',
            sortFunction(a, b) {
              const priorities = {
                High: 3,
                Medium: 2,
                Low: 1
              }
              const value = priorities[a.priority] - priorities[b.priority]
              return value
            },
            order: 'desc',
            sortable: true
          }
        }
      }
    },
    methods: {
      handleSort(sort) {
        const field = sort.field ? this.fields[sort.field] : this.fields[this.currentSort]
        if(sort.sortable) {
          this.currentSort = sort.field
          this.$set(field, 'order', sort.order)
        } else {
          field.order = 'both'
          this.currentSort = null
        }
      },
      sortRows(sort) {
        const result = [...this.practices]

        if(sort.sortFunction instanceof Function){
          result.sort(sort.sortFunction)
        } else {
          result.sort( (a,b) => {
            const fieldA = a[sort.field]
            const fieldB = b[sort.field]
            if( fieldA > fieldB ) return 1
            if( fieldA < fieldB ) return -1
            return 0
          })
        }

        if(sort.order == 'desc') result.reverse()

       return result

      }
    },
    watch: {
      currentPage() {
        const url = Url(window.location.href)
        const query = qs.parse(url.query)
      }
    },
    computed: {
      field(){
        return this.fields[this.currentSort]
      },
      sort(){
        const field = this.field
        return {
          field: field.field,
          order: field.order
        }
      },
      startRow(){
        const start = (this.currentPage - 1) * this.perPage + 1
        return start
      },
      lastRow(){
        const last = this.startRow + this.perPage - 1
        return last > this.rows ? this.rows : last
      },
      sortedRows(){
        const field = this.field
        if( field.field === null || field.order === null) {
          return this.practices
        }

        return this.sortRows(field).slice(this.startRow - 1, this.perPage + this.startRow - 1)
        //.sort( (a, b) => )

      },
      rows(){
        return this.practices.length
      }
    },
    mounted: function() {
      const url = Url(window.location.href)
      const query = qs.parse(url.query)
      if(query.page){
        this.currentPage = query.page * 1
      }
    }
  }
</script>

<style scoped>
  .search-bar-container >>> [role=group]{
    display: flex;
    background: #f7f7f7;
    padding: 7px;
    align-items: center;
  }

  .title {
    color: rgb(33, 37, 41);
    padding: 7px;
    text-align:right;
    justify-self:flex-end;
    width: 75%;
    font-weight: bold;
    text-transform: uppercase;
  }
</style>

<style lang="scss">
  th:last-of-type {
    text-align: right;
  }
  th:first-of-type {
    width: 75%;
  }

/** Fixes to drupal styles */
.table#listing-practice-table,
 .text-long table#listing-practice-table
{
  background-color: none;
}
/* TODO: use variables for colors*/

  .pagination-detail .page-link {
    height: 100%;
  }
  .pagination {
  display: flex;
  padding-left: 0;
  list-style: none; }

.page-link {
  position: relative;
  display: block;
  padding: 0.5rem 0.75rem;
  margin-left: -1px;
  line-height: 1.25;
  color: #b00;
  background-color: #fff;
  border: 1px solid #dee2e6; }
  .page-link:hover {
    z-index: 2;
    color: #6f0000;
    text-decoration: none;
    background-color: #e9ecef;
    border-color: #dee2e6; }
  .page-link:focus {
    z-index: 2;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(187, 0, 0, 0.25); }
  .page-link:not(:disabled):not(.disabled) {
    cursor: pointer; }

.page-item:first-child .page-link {
  margin-left: 0; }

.page-item.active .page-link {
  z-index: 1;
  color: #fff;
  background-color: #b00;
  border-color: #b00; }

.page-item.disabled .page-link {
  color: #6c757d;
  pointer-events: none;
  cursor: auto;
  background-color: #fff;
  border-color: #dee2e6; }

.pagination-lg .page-link {
  padding: 0.75rem 1.5rem;
  font-size: 1.25rem;
  line-height: 1.5; }

.pagination-sm .page-link {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  line-height: 1.5; }

</style>
